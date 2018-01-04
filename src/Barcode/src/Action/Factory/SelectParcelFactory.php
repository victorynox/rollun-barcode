<?php


namespace rollun\barcode\Action\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\barcode\Action\SelectParcel;
use rollun\barcode\DataStore\BarcodeInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SelectParcelFactory
 * @package rollun\barcode\Action\Factory
 */
class SelectParcelFactory implements FactoryInterface
{
    const KEY = SelectParcelFactory::class;

    /**
     * Barcode dataStore service name
     * Non required. By default use rollun\barcode\DataStore\BarcodeInterface::class
     */
    const KEY_BARCODE_DATASTORE_SERVICE = "barcodeDataStoreService";

    /**
     * Create an object
     * [
     *      SelectParcelFactory::KEY_BARCODE_DATASTORE_SERVICE => BarcodeTable::class
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

        return new SelectParcel($barcodeDataStore);
    }
}