<?php


namespace rollun\barcode\Action\Admin;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\actionrender\Renderer\Html\HtmlParamResolver;

class ScansInfo implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $responseData = [
            "title" => "Scans info",
            "table" => [
                "title" => "Scans logs",
                "dataStoreName" => "ScansInfo",
            ]
        ];

        //We have priority by merged data
        $responseData = array_merge_recursive($request->getAttribute("responseData", []), $responseData);

        $request = $request->withAttribute('responseData', $responseData);
        $request = $request->withAttribute(HtmlParamResolver::KEY_ATTRIBUTE_TEMPLATE_NAME, "barcode::admin/scans-info");

        $response = $delegate->process($request);
        return $response;
    }
}