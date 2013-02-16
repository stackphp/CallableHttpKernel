# Stack/CallableHttpKernel

HttpKernelInterface implementation based on callables.

It's mostly useful to test stack middlewares, and to mock the
HttpKernelInterface on the fly.

## Example

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    $app = new CallableHttpKernel(function (Request $request) {
        return new Response('Hello World!');
    });
