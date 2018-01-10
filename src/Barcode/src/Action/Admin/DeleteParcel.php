<?php


namespace rollun\barcode\Action\Admin;


use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\actionrender\Renderer\Html\HtmlParamResolver;
use rollun\barcode\DataStore\Factory\ParcelBarcodeAspectAbstractFactory;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Helper\UrlHelper;

class DeleteParcel extends ParcelAbstract
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
        $queryParams = $request->getQueryParams();

        $responseData = [
            "parcelNumber" => $parcelNumber,
        ];

        if(isset($queryParams["confirm"]) && $queryParams["confirm"] == true) {
            $this->barcodeDataStore->deleteParcel($parcelNumber);
            $responseData["confirm"] = true;
        }

        //We have priority by merged data
        $responseData = array_merge_recursive($request->getAttribute("responseData", []), $responseData);

        $request = $request->withAttribute('responseData', $responseData);
        $request = $request->withAttribute(HtmlParamResolver::KEY_ATTRIBUTE_TEMPLATE_NAME, "barcode::admin/delete-parcel");

        $response = $delegate->process($request);
        return $response;
    }
}