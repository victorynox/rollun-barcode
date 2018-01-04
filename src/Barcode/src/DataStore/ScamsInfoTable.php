<?php

namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\SerializedDbTable;

class ScamsInfoTable extends SerializedDbTable implements ScansInfoInterface
{
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return static::FIELD_ID;
    }
}