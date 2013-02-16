<?php

namespace Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CallableHttpKernelTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function handleShouldDelegateToCallable()
    {
        $spy = new SpyCallable(new Response('ok'));
        $kernel = new CallableHttpKernel($spy);

        $request = Request::create('/');
        $response = $kernel->handle($request);

        $this->assertSame('ok', $response->getContent());
        $this->assertCount(1, $spy);
        $this->assertSame(
            [$request, HttpKernelInterface::MASTER_REQUEST, true],
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
        $this->assertCount(1, $spy);
        $this->assertSame(
            [$request, HttpKernelInterface::SUB_REQUEST, false],
            $spy->getCall(0)
        );
    }
}

class SpyCallable implements \Countable
{
    private $returnValue;
    private $calls = [];

    public function __construct($returnValue)
    {
        $this->returnValue = $returnValue;
    }

    public function __invoke()
    {
        $this->calls[] = func_get_args();

        return $this->returnValue;
    }

    public function count()
    {
        return count($this->calls);
    }

    public function getCall($n)
    {
        return $this->calls[$n];
    }
}
