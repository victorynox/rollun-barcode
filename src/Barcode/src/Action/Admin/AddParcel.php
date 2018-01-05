<?php


namespace rollun\barcode\Action\Admin;


use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\actionrender\Renderer\Html\HtmlParamResolver;
use rollun\barcode\DataStore\BarcodeInterface;
use rollun\barcode\DataStore\Factory\ParcelBarcodeAspectAbstractFactory;
use Zend\Expressive\Exception\InvalidMiddlewareException;

class AddParcel extends ParcelAbstract
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
        $this->barcodeDataStore->create([
            BarcodeInterface::FIELD_PARCEL_NUMBER => $parcelNumber,
            BarcodeInterface::FIELD_ID => "empty_" . time(),
            BarcodeInterface::FIELD_QUANTITY_DATA => "[]",
            BarcodeInterface::FIELD_FNSKU => "remove_me",
            BarcodeInterface::FIELD_IMAGE_LINK => "remove_me",
            BarcodeInterface::FIELD_PART_NUMBER => "remove_me",
        ]);
        $responseData = [
            "parcelNumber" => $parcelNumber,
            "table" => [
                "title" => "New $parcelNumber Parcel",
                "dataStoreName" => ParcelBarcodeAspectAbstractFactory::SERVICE_NAME_PREFIX . $parcelNumber
            ],
        ];
        $request = $request->withAttribute('responseData', $responseData);
        $request = $request->withAttribute(HtmlParamResolver::KEY_ATTRIBUTE_TEMPLATE_NAME, "barcode::admin/add-parcel");
        $response = $delegate->process($request);
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @throws InvalidMiddlewareException
     * @return mixed
     */
    protected function resolveParcelNumber(ServerRequestInterface $request)
    {
        $queryParams = $request->getQueryParams();
        if (!isset($queryParams[static::KEY_ATTRIBUTE_PARCEL_NUMBER])) {
            throw new InvalidMiddlewareException("Not set " . static::KEY_ATTRIBUTE_PARCEL_NUMBER . " attribute.");
        }
        return $queryParams[static::KEY_ATTRIBUTE_PARCEL_NUMBER];
    }
}