<?php

namespace rollun\barcode;

use rollun\amazon\Api\Middleware\Factory\RockyBarcodeFactory;
use rollun\amazon\Api\Middleware\Factory\StatsBarcodeFactory;
use rollun\barcode\Action\RockyBarcodeAction;
use rollun\barcode\Action\StatsBarcodeAction;
use rollun\barcode\DataStore\ScansInfo;
use rollun\datastore\DataStore\CsvBase;
use rollun\datastore\DataStore\Factory\CsvAbstractFactory;
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
            CsvAbstractFactory::KEY_DATASTORE => $this->getDataStore(),
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
            'factories' => [
                RockyBarcodeAction::class => RockyBarcodeFactory::class,
                StatsBarcodeAction::class => StatsBarcodeFactory::class,
            ],
        ];
    }

    /**
     * Returns the dataStore config
     * @return array
     */
    public function getDataStore()
    {
        return [
            ScansInfo::class => [
                CsvAbstractFactory::KEY_CLASS => CsvBase::class,
                CsvAbstractFactory::KEY_FILENAME => Command::getDataDir() . "barcode" . DIRECTORY_SEPARATOR . "barcodeScansInfo.csv",
                CsvAbstractFactory::KEY_DELIMITER => ",",
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
                'error' => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
            ],
        ];
    }
}
