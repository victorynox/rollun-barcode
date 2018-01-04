<?php


namespace rollun\barcode\DataStore;


use rollun\datastore\DataStore\Interfaces\DataStoresInterface;

interface ScansInfoInterface extends DataStoresInterface
{
    const FIELD_ID = "id";

    const FIELD_IP = "ip";

    const FIELD_FNSKU = "fnsku";

    const FIELD_SCAN_TIME = "scanTime";

    const FIELD_PARCEL_NUMBER = "parcel_number";
}