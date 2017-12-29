<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.05.17
 * Time: 13:49
 */

namespace rollun\barcode\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\barcode\BarcodeDataStorePluginManager;
use rollun\barcode\DataStore\ScansInfo;
use rollun\barcode\DirectoryScanner;
use rollun\datastore\DataStore\Interfaces\DataStoresInterface;
use rollun\datastore\Rql\RqlQuery;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Query;

class RockyBarcodeAction implements MiddlewareInterface
{
    const BARCODE_STORAGE_NAME = 'barcodeStorageName';
    const BARCODE = 'barcode';

    /**
     * @var BarcodeDataStorePluginManager
     */
    protected $barcodeStoragePluginManager;

    /**
     * @var DataStoresInterface
     */
    protected $scanBarcode;

    /**
     * RockyBarcodeAction constructor.
     * @param BarcodeDataStorePluginManager $barcodeStoragePluginManager
     * @param DataStoresInterface $scanBarcode
     */
    public function __construct(BarcodeDataStorePluginManager $barcodeStoragePluginManager, DataStoresInterface $scanBarcode)
    {
        $this->barcodeStoragePluginManager = $barcodeStoragePluginManager;
        $this->scanBarcode = $scanBarcode;
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
        $dirScanner = new DirectoryScanner();
        $barcodeFile = array_keys($dirScanner->scanDirectory());

        $queryParams = $request->getQueryParams();
        $responseData = [
            "selectBarcodeStore" => isset($queryParams['selectBarcodeStore']) ? $queryParams['selectBarcodeStore'] : null,
            "barcodeStores" => $barcodeFile,
        ];
        if (isset($queryParams[self::BARCODE_STORAGE_NAME])) {
            $barcodeStorageName = $queryParams[self::BARCODE_STORAGE_NAME];
            /** @var DataStoresInterface $barcodeStore */
            $barcodeStore = $this->barcodeStoragePluginManager->get($barcodeStorageName);
            if (isset($queryParams[self::BARCODE])) {
                $barcode = $queryParams[self::BARCODE];
                $query = new Query();
                $query->setQuery(new EqNode(ScansInfo::FIELD_FNSKU, $barcode));
                $result = $barcodeStore->query($query);
                $this->scanBarcode->create([
                    ScansInfo::FIELD_FNSKU => $barcode,
                    ScansInfo::FIELD_SCAN_TIME => time(),
                    ScansInfo::FIELD_BARCODE_STORAGE_NAME => $barcodeStorageName,
                    ScansInfo::FIELD_IP => $this->getClientIp($request)
                ]);
                if (empty($result)) {
                    $responseData["notify"] = "Товар с barcode: $barcode не найден";
                } else {
                    $responseData["info"] = $result[0];
                }
                $request = $request->withAttribute('responseData', $responseData);
            }
        }

        $response = $delegate->process($request);
        return $response;
    }

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
}
