<?php


namespace rollun\barcode\Middleware;


use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Cunt class
 * Class ParcelNumberResolver
 * @package rollun\barcode\Middleware
 */
class ParcelNumberResolver implements MiddlewareInterface
{
    const KEY_ATTRIBUTE_PARCEL_NUMBER = "parcel_number";

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
        $parcelNumber = $request->getAttribute(static::KEY_ATTRIBUTE_PARCEL_NUMBER);
        if (is_null($parcelNumber)) {
            $queryParams = $request->getQueryParams();
            $parcelNumber = isset($queryParams[static::KEY_ATTRIBUTE_PARCEL_NUMBER]) ? $queryParams[static::KEY_ATTRIBUTE_PARCEL_NUMBER] : "";
        }
        $parcelNumber = trim($parcelNumber);
        $parcelNumber = urldecode($parcelNumber);

        $request = $request->withAttribute(static::KEY_ATTRIBUTE_PARCEL_NUMBER, empty($parcelNumber) ? null : $parcelNumber);
        $response = $delegate->process($request);
        return $response;
    }
}