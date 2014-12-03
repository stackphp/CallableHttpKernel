<?php

namespace Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CallableHttpKernelTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function handleAndTerminateShouldDelegateToCallable()
    {
        $handleSpy = new SpyCallable(new Response('ok'));
        $terminateSpy = new SpyCallable(null);
        $kernel = new CallableHttpKernel($handleSpy, $terminateSpy);

        $request = Request::create('/');
        $response = $kernel->handle($request);

        $this->assertSame('ok', $response->getContent());
        $this->assertSame(1, $handleSpy->getCount());
        $this->assertSame(
            array($request, HttpKernelInterface::MASTER_REQUEST, true),
            $handleSpy->getCall(0)
        );

        $kernel->terminate($request, $response);

        $this->assertSame(1, $terminateSpy->getCount());
        $this->assertSame(
            array($request, $response),
            $terminateSpy->getCall(0)
        );
    }

    /** @test */
    public function handleShouldDelegateAllArgs()
    {
        $handleSpy = new SpyCallable(new Response('ok'));
        $kernel = new CallableHttpKernel($handleSpy);

        $request = Request::create('/');
        $response = $kernel->handle($request, HttpKernelInterface::SUB_REQUEST, false);

        $this->assertSame('ok', $response->getContent());
        $this->assertSame(1, $handleSpy->getCount());
        $this->assertSame(
            array($request, HttpKernelInterface::SUB_REQUEST, false),
            $handleSpy->getCall(0)
        );
    }
}

class SpyCallable
{
    private $returnValue;
    private $calls = array();

    public function __construct($returnValue)
    {
        $this->returnValue = $returnValue;
    }

    public function __invoke()
    {
        $this->calls[] = func_get_args();

        return $this->returnValue;
    }

    public function getCount()
    {
        return count($this->calls);
    }

    public function getCall($n)
    {
        return $this->calls[$n];
    }
}
