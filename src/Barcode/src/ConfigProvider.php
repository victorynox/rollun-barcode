<?php

namespace rollun\barcode;

use rollun\actionrender\Factory\ActionRenderAbstractFactory;
use rollun\barcode\Action\SearchBarcode;
use rollun\barcode\Action\Factory\SearchBarcodeFactory;
use rollun\barcode\Action\Factory\SelectParcelFactory;
use rollun\barcode\Action\SelectParcel;
use rollun\barcode\DataStore\BarcodeAspect;
use rollun\barcode\DataStore\BarcodeCsv;
use rollun\barcode\DataStore\BarcodeInterface;
use rollun\barcode\DataStore\Factory\BarcodeAspectAbstractFactory;
use rollun\barcode\DataStore\ScansInfoCsv;
use rollun\barcode\DataStore\ScansInfoInterface;
use rollun\datastore\DataStore\Factory\CsvAbstractFactory;
use rollun\datastore\DataStore\Factory\DataStoreAbstractFactory;
use rollun\installer\Command;

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
                BarcodeAspectAbstractFactory::class
            ],
            'factories' => [
                SearchBarcode::class => SearchBarcodeFactory::class,
                SelectParcel::class =>  SelectParcelFactory::class,
            ],
            "aliases" => [
                BarcodeInterface::class => BarcodeCsv::class,
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
            BarcodeAspect::class => [
                BarcodeAspectAbstractFactory::KEY_DATASTORE => BarcodeInterface::class,
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
            ]
        ];
    }
}
