<?php

namespace Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

class CallableHttpKernel implements HttpKernelInterface, TerminableInterface
{
    private $handleCallable;
    private $terminateCallable;

    public function __construct($handleCallable, $terminateCallable = null)
    {
        if (!is_callable($handleCallable)) {
            throw new \InvalidArgumentException('Invalid handleCallable passed to CallableHttpKernel::__construct().');
        }

        if ($terminateCallable && !is_callable($terminateCallable)) {
            throw new \InvalidArgumentException('Invalid terminateCallable passed to CallableHttpKernel::__construct().');
        }

        $this->handleCallable = $handleCallable;
        $this->terminateCallable = $terminateCallable;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = call_user_func($this->handleCallable, $request, $type, $catch);

        if (!$response instanceof Response) {
            throw new \UnexpectedValueException('Kernel function did not return an object of type Response');
        }

        return $response;
    }

    public function terminate(Request $request, Response $response)
    {
        if (!$this->terminateCallable) {
            return;
        }

        call_user_func($this->terminateCallable, $request, $response);
    }
}
