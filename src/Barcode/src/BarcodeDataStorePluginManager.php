<?php


namespace rollun\barcode;

use rollun\barcode\DataStore\Barcode as BarcodeDataStore;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;

class BarcodeDataStorePluginManager extends AbstractPluginManager
{
    protected $instanceOf = BarcodeDataStore::class;

    public function validate($instance)
    {
        if($instance instanceof BarcodeDataStore) {
            return;
        }
        throw new InvalidServiceException("This is not a valid service.");
    }

}