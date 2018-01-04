<?php

namespace rollun\barcode\Action\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\barcode\Action\SearchBarcode;
use rollun\barcode\DataStore\BarcodeInterface;
use rollun\barcode\DataStore\ScansInfoInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class SearchBarcodeFactory implements FactoryInterface
{
    const KEY = SearchBarcodeFactory::class;

    /**
     * Barcode dataStore service name
     * Non required. By default use rollun\barcode\DataStore\BarcodeInterface::class
     */
    const KEY_BARCODE_DATASTORE_SERVICE = "barcodeDataStoreService";

    /**
     * ScansInfo dataStore service name
     * Non required. By default use rollun\barcode\DataStore\ScansInfoInterface::class
     */
    const KEY_SCANS_INFO_DATASTORE_SERVICE = "scansInfoDataStoreService";
    /**
     * Create an object
     * [
     *      SearchBarcodeFactory::KEY_BARCODE_DATASTORE_SERVICE => BarcodeCsv::class,
     *      SearchBarcodeFactory::KEY_SCANS_INFO_DATASTORE_SERVICE => ScamsInfoTable::class,
     * ]
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        try {
            $config = $container->get("config");
        } catch (NotFoundExceptionInterface $e) {
            throw new ServiceNotCreatedException("Not found config in container.", $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get config from container", $e->getCode(), $e);
        }
        //get barcode dataStore
        $barcodeDataStoreServiceName = isset($config[static::KEY][static::KEY_BARCODE_DATASTORE_SERVICE]) ?
            $config[static::KEY][static::KEY_BARCODE_DATASTORE_SERVICE] : BarcodeInterface::class;
        try {
            $barcodeDataStore = $container->get($barcodeDataStoreServiceName);
        } catch (NotFoundExceptionInterface $e) {
            throw new ServiceNotCreatedException("Not found $barcodeDataStoreServiceName in container.", $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get $barcodeDataStoreServiceName from container", $e->getCode(), $e);
        }

        //get scansInfo dataStore
        $scansInfoDataStoreServiceName = isset($config[static::KEY][static::KEY_SCANS_INFO_DATASTORE_SERVICE]) ?
            $config[static::KEY][static::KEY_SCANS_INFO_DATASTORE_SERVICE] : ScansInfoInterface::class;
        try {
            $scansInfoDataStore = $container->get($scansInfoDataStoreServiceName);
        } catch (NotFoundExceptionInterface $e) {
            throw new ServiceNotCreatedException("Not found $scansInfoDataStoreServiceName in container.", $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get $scansInfoDataStoreServiceName from container", $e->getCode(), $e);
        }

        return new SearchBarcode($barcodeDataStore, $scansInfoDataStore);
    }
}