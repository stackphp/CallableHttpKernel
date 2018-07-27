<?php

namespace Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CallableHttpKernelTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function handleShouldDelegateToCallable()
    {
        $spy = new SpyCallable(new Response('ok'));
        $kernel = new CallableHttpKernel($spy);

        $request = Request::create('/');
        $response = $kernel->handle($request);

        $this->assertSame('ok', $response->getContent());
        $this->assertSame(1, $spy->getCount());
        $this->assertSame(
            array($request, HttpKernelInterface::MASTER_REQUEST, true),
            $spy->getCall(0)
        );
    }

    /** @test */
    public function handleShouldDelegateAllArgs()
    {
        $spy = new SpyCallable(new Response('ok'));
        $kernel = new CallableHttpKernel($spy);

        $request = Request::create('/');
        $response = $kernel->handle($request, HttpKernelInterface::SUB_REQUEST, false);

        $this->assertSame('ok', $response->getContent());
        $this->assertSame(1, $spy->getCount());
        $this->assertSame(
            array($request, HttpKernelInterface::SUB_REQUEST, false),
            $spy->getCall(0)
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
