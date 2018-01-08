<?php

namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\Interfaces\DataStoresInterface;

interface BarcodeInterface extends DataStoresInterface
{
    /** @const string - define */
    const FIELD_ID = self::DEF_ID;

    /** @const string - define */
    const FIELD_FNSKU = "fnsku";

    /** @const string - define */
    const FIELD_PART_NUMBER = "part_number";

    /** @const string - define */
    const FIELD_PARCEL_NUMBER = "parcel_number";

    /** @const string - define */
    const FIELD_IMAGE_LINK = "image_link";

    /** @const string - define */
    const FIELD_QUANTITY_DATA = "quantity";

    /**
     * Return array with all different parcel name.
     * [
     *    "12ms931s",
     *    "1656m12931s",
     *    "16Av56m123",
     * ]
     * @return array
     */
    public function getParcelNumbers();

    /**
     * Remove all item which contained in selected parcel.
     * @param $parcelNumber
     * @return void
     */
    public function deleteParcel($parcelNumber);

    /**
     * @param $parcelNumber
     * @return boolean
     */
    public function hasParcel($parcelNumber);
}