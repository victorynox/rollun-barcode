<?php


namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\CsvBase;

class ScansInfoCsv extends CsvBase implements ScansInfoInterface
{
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return static::FIELD_ID;
    }
}