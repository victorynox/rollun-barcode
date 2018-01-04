<?php


namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\SerializedDbTable;

class BarcodeTable extends SerializedDbTable implements BarcodeInterface
{
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return static::FIELD_ID;
    }
}