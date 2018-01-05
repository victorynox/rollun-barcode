<?php


namespace rollun\barcode\Action\Admin;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use rollun\actionrender\Renderer\Html\HtmlParamResolver;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\barcode\DataStore\Factory\ParcelBarcodeAspectAbstractFactory;
use Zend\Expressive\Exception\InvalidMiddlewareException;

class EditParcel extends ParcelAbstract
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
        $parcelNumber = $this->resolveParcelNumber($request);
        if(!$this->barcodeDataStore->hasParcel($parcelNumber)) {
            throw new InvalidMiddlewareException("Parcel $parcelNumber not exist.");
        }
        $responseData = [
            "parcelNumber" => $parcelNumber,
            "table" => [
                "title" => "Edit $parcelNumber Parcel",
                "dataStoreName" => ParcelBarcodeAspectAbstractFactory::SERVICE_NAME_PREFIX . $parcelNumber
            ],
        ];

        $request = $request->withAttribute('responseData', $responseData);
        $request = $request->withAttribute(HtmlParamResolver::KEY_ATTRIBUTE_TEMPLATE_NAME, "barcode::admin/edit-parcel");

        $response = $delegate->process($request);
        return $response;
    }
}