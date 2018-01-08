<?php

namespace rollun\barcode\DataStore;

use rollun\barcode\DataStore\Traits\BarcodeTrait;
use rollun\datastore\DataStore\CsvBase;

class BarcodeCsv extends CsvBase implements BarcodeInterface
{
    use BarcodeTrait;

}