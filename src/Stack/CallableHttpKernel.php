<?php

namespace Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CallableHttpKernel implements HttpKernelInterface
{
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        return call_user_func($this->callable, $request, $type, $catch);
    }
}
