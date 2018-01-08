<?php


namespace rollun\barcode\Action;


use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\actionrender\Renderer\Html\HtmlParamResolver;
use rollun\barcode\DataStore\BarcodeInterface as BarcodeDataStoreInterface;
use rollun\datastore\Rql\Node\AggregateSelectNode;
use rollun\datastore\Rql\Node\GroupbyNode;
use rollun\datastore\Rql\RqlQuery;
use Xiag\Rql\Parser\Query;

/**
 * Class SelectParcel return list of barcode parcel.
 * @package rollun\barcode\Action
 */
class SelectParcel implements MiddlewareInterface
{
    /** @var BarcodeDataStoreInterface */
    protected $barcodeDataStore;

    /**
     * SelectParcel constructor.
     * @param BarcodeDataStoreInterface $barcodeDataStore
     */
    public function __construct(BarcodeDataStoreInterface $barcodeDataStore)
    {
        $this->barcodeDataStore = $barcodeDataStore;
    }

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
            'title' => "Select parcel",
            'parcelNumbers' => $this->barcodeDataStore->getParcelNumbers()
        ];

        //We have priority by merged data
        $responseData = array_merge_recursive($request->getAttribute("responseData", []), $responseData);

        $request = $request->withAttribute("responseData", $responseData);
        $request = $request->withAttribute(HtmlParamResolver::KEY_ATTRIBUTE_TEMPLATE_NAME, "barcode::select-parcel");
        $response = $delegate->process($request);
        return $response;
    }
}