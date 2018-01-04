<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.05.17
 * Time: 14:57
 */

namespace rollun\amazon\Api\Middleware\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\barcode\Action\RockyBarcodeAction;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class RockyBarcodeFactory implements FactoryInterface
{

    const KEY = "keyRockyBarcode";

    const KEY_BARCODE_PLUGIN_MANAGER = "keyBarcodePluginManager";

    const KEY_SCAN_BARCODE_DS = "keyScansBarcodeDS";

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
        if (empty($options)) {
            try {
                $config = $container->get('config');
            } catch (NotFoundExceptionInterface $e) {
                throw new ServiceNotCreatedException("Config not created", $e->getCode(), $e);
            } catch (ContainerExceptionInterface $e) {
                throw new ServiceNotCreatedException("Config not created", $e->getCode(), $e);
            }
            $serviceConfig = $config[static::KEY];
        } else {
            $serviceConfig = $options;
        }
        try {
            $barcodePluginManager = $container->get($serviceConfig[static::KEY_BARCODE_PLUGIN_MANAGER]);
        } catch (NotFoundExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't created " . $serviceConfig[static::KEY_BARCODE_PLUGIN_MANAGER], $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't created " . $serviceConfig[static::KEY_BARCODE_PLUGIN_MANAGER], $e->getCode(), $e);
        }
        try {
            $scansBarcode = $container->get($serviceConfig[static::KEY_SCAN_BARCODE_DS]);
        } catch (NotFoundExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't created " . $serviceConfig[static::KEY_SCAN_BARCODE_DS], $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't created " . $serviceConfig[static::KEY_SCAN_BARCODE_DS], $e->getCode(), $e);
        }
        return new RockyBarcodeAction($barcodePluginManager, $scansBarcode);
    }
}
