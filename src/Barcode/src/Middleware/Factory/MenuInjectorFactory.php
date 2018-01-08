<?php


namespace rollun\barcode\Middleware\Factory;


use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\barcode\Middleware\MenuInjector;
use Zend\Expressive\Helper\UrlHelper;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class MenuInjectorFactory implements FactoryInterface
{
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
        //get urlHelper
        $urlHelperServiceName = UrlHelper::class;
        try {
            $urlHelper = $container->get($urlHelperServiceName);
        } catch (NotFoundExceptionInterface $e) {
            throw new ServiceNotCreatedException("Not found $urlHelperServiceName in container.", $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get $urlHelperServiceName from container", $e->getCode(), $e);
        }
        return new MenuInjector($urlHelper);
    }
}