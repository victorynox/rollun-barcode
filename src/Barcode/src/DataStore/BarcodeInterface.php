<?php

namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\Interfaces\DataStoresInterface;

interface BarcodeInterface extends DataStoresInterface
{
    const FIELD_ID = self::DEF_ID;

    const FIELD_FNSKU = "fnsku";

    const FIELD_PART_NUMBER = "part_number";

    const FIELD_PARCEL_NUMBER = "parcel_number";

    const FIELD_IMAGE_LINK = "image_link";

    const FIELD_QUANTITY_DATA = "quantity";
}