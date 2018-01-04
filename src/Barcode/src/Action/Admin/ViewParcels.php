<?php


namespace rollun\barcode\Action\Admin;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\actionrender\Renderer\Html\HtmlParamResolver;
use rollun\barcode\DataStore\BarcodeInterface as BarcodeDataStoreInterface;

/**
 * Class ViewParcel
 * Return list of parcel to further management
 * @package rollun\barcode\Action\Admin
 */
class ViewParcels extends ParcelAbstract
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
        $parcelNumbers = $this->barcodeDataStore->getParcelNumbers();
        $responseData = [
            'title' => "Admin Parcel",
            'parcelNumbers' => $parcelNumbers,
        ];
        $request = $request->withAttribute('responseData', $responseData);
        $request = $request->withAttribute(HtmlParamResolver::KEY_ATTRIBUTE_TEMPLATE_NAME, "barcode::admin/view-parcels");

        $response = $delegate->process($request);
        return $response;
    }
}