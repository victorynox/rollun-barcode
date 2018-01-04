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
        $query = new RqlQuery();
        $query->setSelect(new AggregateSelectNode([
            "count(".$this->barcodeDataStore->getIdentifier().")",
            BarcodeDataStoreInterface::FIELD_PARCEL_NUMBER
        ]));
        $query->setGroupby(new GroupbyNode([BarcodeDataStoreInterface::FIELD_PARCEL_NUMBER]));
        $result = $this->barcodeDataStore->query($query);
        $parcelNumbers = [];
        foreach ($result as $item) {
            $parcelNumbers[] = $item[BarcodeDataStoreInterface::FIELD_PARCEL_NUMBER];
        }
        $responseData = [
            'title' => "Select parcel",
            'parcelNumbers' => $parcelNumbers
        ];
        $request = $request->withAttribute("responseData", $responseData);
        $request = $request->withAttribute(HtmlParamResolver::KEY_ATTRIBUTE_TEMPLATE_NAME, "barcode::select-parcel");
        $response = $delegate->process($request);
        return $response;
    }
}