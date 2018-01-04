<?php

namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\CsvBase;

class BarcodeCsv extends CsvBase implements BarcodeInterface
{
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return static::FIELD_ID;
    }
}