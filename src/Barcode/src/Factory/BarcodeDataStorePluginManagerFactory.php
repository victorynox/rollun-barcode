<?php


namespace rollun\barcode\Factory;


use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\barcode\BarcodeDataStorePluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class BarcodeDataStorePluginManagerFactory implements FactoryInterface
{

    const KEY = BarcodeDataStorePluginManagerFactory::class;

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
            throw new ServiceNotCreatedException("Config not found.", $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Config not found.", $e->getCode(), $e);
        }
        $barcodePluginManagerDepConfig = $config[static::KEY];
        return new BarcodeDataStorePluginManager($container, $barcodePluginManagerDepConfig);
    }
}