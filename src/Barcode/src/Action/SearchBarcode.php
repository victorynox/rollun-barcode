<?php


namespace rollun\barcode\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\actionrender\Renderer\Html\HtmlParamResolver;
use rollun\barcode\DataStore\BarcodeInterface as BarcodeDataStoreInterface;
use rollun\barcode\DataStore\Factory\ParcelBarcodeAspectAbstractFactory;
use rollun\barcode\DataStore\ScansInfoInterface as ScansInfoDataStoreInterface;
use rollun\utils\Json\Serializer;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Query;
use Zend\Expressive\Exception\InvalidMiddlewareException;

/**
 * Class BarcodeView
 * Return info by searched barcode in selected parcel.
 * @package rollun\barcode\Action\Factory
 */
class SearchBarcode implements MiddlewareInterface
{
    const KEY_ATTRIBUTE_PARCEL_NUMBER = "parcel_number";

    const KEY_QUERY_BARCODE = "barcode";

    /** @var BarcodeDataStoreInterface */
    protected $barcodeDataStore;

    /**
     * @var ScansInfoDataStoreInterface
     */
    protected $scansInfoDataStore;

    /**
     * BarcodeView constructor.
     * @param BarcodeDataStoreInterface $barcodeDataStore
     * @param ScansInfoDataStoreInterface $scansInfoDataStore
     */
    public function __construct(BarcodeDataStoreInterface $barcodeDataStore, ScansInfoDataStoreInterface $scansInfoDataStore)
    {
        $this->barcodeDataStore = $barcodeDataStore;
        $this->scansInfoDataStore = $scansInfoDataStore;
    }

    /**
     * //TODO: maybe need to remove to service
     * Return request client IP.
     * @param ServerRequestInterface $request
     * @return string
     */
    public function getClientIp(ServerRequestInterface $request)
    {
        $serverParam = $request->getServerParams();
        if (isset($serverParam['HTTP_CLIENT_IP'])) {
            $ipAddress = $serverParam['HTTP_CLIENT_IP'];
        } elseif (isset($serverParam['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $serverParam['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($serverParam['HTTP_X_FORWARDED'])) {
            $ipAddress = $serverParam['HTTP_X_FORWARDED'];
        } elseif (isset($serverParam['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $serverParam['HTTP_FORWARDED_FOR'];
        } elseif (isset($serverParam['HTTP_FORWARDED'])) {
            $ipAddress = $serverParam['HTTP_FORWARDED'];
        } elseif (isset($serverParam['REMOTE_ADDR'])) {
            $ipAddress = $serverParam['REMOTE_ADDR'];
        } else {
            $ipAddress = 'UNKNOWN';
        }
        return $ipAddress;
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
        $parcelNumber = $request->getAttribute(static::KEY_ATTRIBUTE_PARCEL_NUMBER);
        if (is_null($parcelNumber)) {
            throw new InvalidMiddlewareException("Not set " . static::KEY_ATTRIBUTE_PARCEL_NUMBER . " attribute.");
        }

        //prepare template data
        $responseData = [
            "title" => "Search barcode",
            "parcelNumber" => $parcelNumber,
        ];

        $queryParams = $request->getQueryParams();
        if (!isset($queryParams[static::KEY_QUERY_BARCODE])) {
            $responseData["barcodeAspectName"] = ParcelBarcodeAspectAbstractFactory::SERVICE_NAME_PREFIX . $parcelNumber;
        } else {
            $fnsku = $queryParams[static::KEY_QUERY_BARCODE];
            //get barcode
            $query = new Query();
            $query->setQuery(new AndNode([
                new EqNode(BarcodeDataStoreInterface::FIELD_PARCEL_NUMBER, $parcelNumber),
                new EqNode(BarcodeDataStoreInterface::FIELD_FNSKU, $fnsku),
            ]));
            $query->setLimit(new LimitNode(1));
            $result = $this->barcodeDataStore->query($query);
            //logged scans
            $this->scansInfoDataStore->create([
                ScansInfoDataStoreInterface::FIELD_FNSKU => $fnsku,
                ScansInfoDataStoreInterface::FIELD_PARCEL_NUMBER => $parcelNumber,
                ScansInfoDataStoreInterface::FIELD_SCAN_TIME => time(),
                ScansInfoDataStoreInterface::FIELD_IP => $this->getClientIp($request),

            ]);

            $responseData["barcode"] = $fnsku;
            if (empty($result)) {
                //TODO: refactor this.
                $responseData['notify'] = "Item not found";
            } else {
                $barcodeInfo = $result[0];
                $barcodeInfo[BarcodeDataStoreInterface::FIELD_QUANTITY_DATA] =
                    is_string($barcodeInfo[BarcodeDataStoreInterface::FIELD_QUANTITY_DATA]) ?
                        Serializer::jsonUnserialize($barcodeInfo[BarcodeDataStoreInterface::FIELD_QUANTITY_DATA]) :
                        $barcodeInfo[BarcodeDataStoreInterface::FIELD_QUANTITY_DATA];
                $responseData['barcodeInfo'] = $barcodeInfo;
            }
        }

        $request = $request->withAttribute('responseData', $responseData);
        $request = $request->withAttribute(HtmlParamResolver::KEY_ATTRIBUTE_TEMPLATE_NAME, "barcode::search-barcode");

        $response = $delegate->process($request);
        return $response;
    }
}