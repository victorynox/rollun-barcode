<?php


namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\CsvBase;

class ScansInfo extends CsvBase
{

    const FIELD_ID = "id";

    const FIELD_FNSKU = "FNSKU";

    const FIELD_SCAN_TIME = "scanTime";

    const FIELD_BARCODE_STORAGE_NAME = "barcodeStorage";

    const FIELD_IP = "ip";
}