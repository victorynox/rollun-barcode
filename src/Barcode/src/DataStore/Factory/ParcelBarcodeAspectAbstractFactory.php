<?php


namespace rollun\barcode\DataStore\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\barcode\DataStore\ParcelBarcodeAspect;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ParcelBarcodeAspectAbstractFactory implements AbstractFactoryInterface
{
    const KEY = "dataStore";

    const KEY_CLASS = ParcelBarcodeAspect::class;

    const KEY_DATASTORE = "dataStore";

    const SERVICE_NAME_PREFIX = "Barcode_";

    const SERVICE_NAME_PATTERN = '/^' . self::SERVICE_NAME_PREFIX . '(?<parcel_number>[\W\w_-]+)$' . '/';

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return preg_match(static::SERVICE_NAME_PATTERN, $requestedName);
    }

    /**
     * Create an object
     * [
     *      ParcelBarcodeAspect::class => [
     *          ParcelBarcodeAspectAbstractFactory::KEY_DATASTORE => Barcode::class,
     *      ]
     * ]
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return ParcelBarcodeAspect
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
            throw new ServiceNotCreatedException("Not found config from container.", $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get config from container.", $e->getCode(), $e);
        }
        if (!isset($config[static::KEY][static::KEY_CLASS])) {
            throw new ServiceNotCreatedException("Service config not found.");
        }
        $serviceConfig = $config[static::KEY][static::KEY_CLASS];
        if (!isset($serviceConfig[static::KEY_DATASTORE])) {
            throw new ServiceNotCreatedException("Original dataStore not set.");
        }
        try {
            $dataStore = $container->get($serviceConfig[static::KEY_DATASTORE]);
        } catch (NotFoundExceptionInterface $e) {
            throw new ServiceNotCreatedException("DataStore " . $serviceConfig[static::KEY_DATASTORE] . " not found.", $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("DataStore " . $serviceConfig[static::KEY_DATASTORE] . " not created.", $e->getCode(), $e);
        }
        if(!preg_match(static::SERVICE_NAME_PATTERN, $requestedName, $match)) {
            throw new ServiceNotCreatedException("Requested service $requestedName is not ParcelBarcodeAspect compatible.");
        }
        $parcelNumber = $match['parcel_number'];
        return new ParcelBarcodeAspect($dataStore, $parcelNumber);
    }
}