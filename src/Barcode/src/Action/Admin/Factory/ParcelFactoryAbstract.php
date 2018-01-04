<?php

namespace rollun\barcode\Action\Admin\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\barcode\Action\Admin\ParcelAbstract;
use rollun\barcode\DataStore\BarcodeInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

abstract class ParcelFactoryAbstract implements FactoryInterface
{
    const KEY = ParcelFactoryAbstract::class;

    /**
     * Barcode dataStore service name
     * Non required. By default use rollun\barcode\DataStore\BarcodeInterface::class
     */
    const KEY_BARCODE_DATASTORE_SERVICE = "barcodeDataStoreService";

    /**
     * Barcode dataStore service name
     * Non required. By default use use Zend\Expressive\Helper\UrlHelper::class
     */
    const KEY_URL_HELPER_SERVICE = "urlHelperService";

    const INSTANCE_CLASS = ParcelAbstract::class;

    /**
     * Create an object
     *
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
        //get urlHelper
        $urlHelperServiceName = isset($config[static::KEY][static::KEY_URL_HELPER_SERVICE]) ?
            $config[static::KEY][static::KEY_URL_HELPER_SERVICE] : UrlHelper::class;
        try {
            $urlHelper = $container->get($urlHelperServiceName);
        } catch (NotFoundExceptionInterface $e) {
            throw new ServiceNotCreatedException("Not found $urlHelperServiceName in container.", $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get $urlHelperServiceName from container", $e->getCode(), $e);
        }

        $class = static::INSTANCE_CLASS;
        return new $class($barcodeDataStore, $urlHelper);
    }
}