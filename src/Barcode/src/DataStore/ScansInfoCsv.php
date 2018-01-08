<?php


namespace rollun\barcode\DataStore;

use rollun\barcode\DataStore\Traits\ScansInfoTrait;
use rollun\datastore\DataStore\CsvBase;

class ScansInfoCsv extends CsvBase implements ScansInfoInterface
{
    use ScansInfoTrait;
}