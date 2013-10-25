<?php

namespace Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class CallableHttpKernel implements HttpKernelInterface
{
    private $callable;

    public function __construct($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('Invalid callable passed to CallableHttpKernel::__construct().');
        }

        $this->callable = $callable;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = call_user_func($this->callable, $request, $type, $catch);

        if (!$response instanceof Response) {
            throw new \UnexpectedValueException('Kernel function did not return an object of type Response');
        }

        return $response;
    }
}
