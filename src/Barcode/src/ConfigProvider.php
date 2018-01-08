<?php

namespace rollun\barcode;

use rollun\actionrender\Factory\ActionRenderAbstractFactory;
use rollun\barcode\Action\Admin;
use rollun\barcode\Action\SearchBarcode;
use rollun\barcode\Action\Factory\SearchBarcodeFactory;
use rollun\barcode\Action\Factory\SelectParcelFactory;
use rollun\barcode\Action\SelectParcel;
use rollun\barcode\DataStore\ParcelBarcodeAspect;
use rollun\barcode\DataStore\BarcodeCsv;
use rollun\barcode\DataStore\BarcodeInterface;
use rollun\barcode\DataStore\Factory\ParcelBarcodeAspectAbstractFactory;
use rollun\barcode\DataStore\ScansInfoCsv;
use rollun\barcode\DataStore\ScansInfoInterface;
use rollun\barcode\Middleware\Factory\MenuInjectorFactory;
use rollun\barcode\Middleware\MenuInjector;
use rollun\datastore\DataStore\Factory\CsvAbstractFactory;
use rollun\datastore\DataStore\Factory\DataStoreAbstractFactory;
use rollun\installer\Command;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            DataStoreAbstractFactory::KEY_DATASTORE => $this->getDataStore(),
            ActionRenderAbstractFactory::KEY => $this->getActionRenderAbstractFactoryConfig(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [],
            "abstract_factories" => [
                ParcelBarcodeAspectAbstractFactory::class
            ],
            'factories' => [
                MenuInjector::class => MenuInjectorFactory::class,
                SearchBarcode::class => SearchBarcodeFactory::class,
                SelectParcel::class => SelectParcelFactory::class,

                Admin\Index::class => InvokableFactory::class,
                Admin\ScansInfo::class => InvokableFactory::class,
                Admin\DeleteParcel::class => Admin\Factory\DeleteParcelFactory::class,
                Admin\EditParcel::class => Admin\Factory\EditParcelFactory::class,
                Admin\AddParcel::class => Admin\Factory\AddParcelFactory::class,
                Admin\ViewParcels::class => Admin\Factory\ViewParcelsFactory::class,

            ],
            "aliases" => [
                BarcodeInterface::class => BarcodeCsv::class,
                "Barcode" => BarcodeInterface::class,
                "ScansInfo" => ScansInfoInterface::class,
                ScansInfoInterface::class => ScansInfoCsv::class
            ]
        ];
    }

    /**
     * Returns the dataStore config
     * @return array
     */
    public function getDataStore()
    {
        return [
            ScansInfoCsv::class => [
                CsvAbstractFactory::KEY_CLASS => ScansInfoCsv::class,
                CsvAbstractFactory::KEY_FILENAME => Command::getDataDir() . "barcode" . DIRECTORY_SEPARATOR . "barcodeScansInfo.csv",
                CsvAbstractFactory::KEY_DELIMITER => ",",
            ],
            BarcodeCsv::class => [
                CsvAbstractFactory::KEY_CLASS => BarcodeCsv::class,
                CsvAbstractFactory::KEY_FILENAME => Command::getDataDir() . "barcode" . DIRECTORY_SEPARATOR . "rockyBarcode.csv",
                CsvAbstractFactory::KEY_DELIMITER => ",",
            ],
            ParcelBarcodeAspect::class => [
                ParcelBarcodeAspectAbstractFactory::KEY_DATASTORE => BarcodeInterface::class,
            ]
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getTemplates()
    {
        return [
            'paths' => [
                'app' => [__DIR__ . '/../templates/app'],
                'barcode' => [__DIR__ . '/../templates/barcode'],
            ],
        ];
    }

    /**
     * Return ActionRender service config
     */
    public function getActionRenderAbstractFactoryConfig()
    {
        return [
            "search-barcode-service" => [
                ActionRenderAbstractFactory::KEY_ACTION_MIDDLEWARE_SERVICE => SearchBarcode::class,
                ActionRenderAbstractFactory::KEY_RENDER_MIDDLEWARE_SERVICE => "simpleHtmlJsonRendererLLPipe",
            ],
            "select-parcel-service" => [
                ActionRenderAbstractFactory::KEY_ACTION_MIDDLEWARE_SERVICE => SelectParcel::class,
                ActionRenderAbstractFactory::KEY_RENDER_MIDDLEWARE_SERVICE => "simpleHtmlJsonRendererLLPipe",
            ],
            //admins
            "admin-index-service" => [
                ActionRenderAbstractFactory::KEY_ACTION_MIDDLEWARE_SERVICE => Admin\Index::class,
                ActionRenderAbstractFactory::KEY_RENDER_MIDDLEWARE_SERVICE => "simpleHtmlJsonRendererLLPipe",
            ],
            "scans-info-service" => [
                ActionRenderAbstractFactory::KEY_ACTION_MIDDLEWARE_SERVICE => Admin\ScansInfo::class,
                ActionRenderAbstractFactory::KEY_RENDER_MIDDLEWARE_SERVICE => "simpleHtmlJsonRendererLLPipe",
            ],
            "delete-parcel-service" => [
                ActionRenderAbstractFactory::KEY_ACTION_MIDDLEWARE_SERVICE => Admin\DeleteParcel::class,
                ActionRenderAbstractFactory::KEY_RENDER_MIDDLEWARE_SERVICE => "simpleHtmlJsonRendererLLPipe",
            ],
            "edit-parcel-service" => [
                ActionRenderAbstractFactory::KEY_ACTION_MIDDLEWARE_SERVICE => Admin\EditParcel::class,
                ActionRenderAbstractFactory::KEY_RENDER_MIDDLEWARE_SERVICE => "simpleHtmlJsonRendererLLPipe",
            ],
            "add-parcel-service" => [
                ActionRenderAbstractFactory::KEY_ACTION_MIDDLEWARE_SERVICE => Admin\AddParcel::class,
                ActionRenderAbstractFactory::KEY_RENDER_MIDDLEWARE_SERVICE => "simpleHtmlJsonRendererLLPipe",
            ],
            "view-parcels-service" => [
                ActionRenderAbstractFactory::KEY_ACTION_MIDDLEWARE_SERVICE => Admin\ViewParcels::class,
                ActionRenderAbstractFactory::KEY_RENDER_MIDDLEWARE_SERVICE => "simpleHtmlJsonRendererLLPipe",
            ],
        ];
    }
}
