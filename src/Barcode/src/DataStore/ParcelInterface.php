<?php


namespace rollun\barcode\DataStore;


use rollun\datastore\DataStore\Interfaces\DataStoresInterface;

interface ParcelInterface extends DataStoresInterface
{
    const FIELD_ID = self::DEF_ID;

    const FIELD_TITLE = "title";
}