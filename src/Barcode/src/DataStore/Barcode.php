<?php

namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\CsvBase;

class Barcode extends CsvBase
{
    const FIELD_ID = self::DEF_ID;

    const FIELD_FNSKU = "FNSKU";

    const FIELD_PART_NUMBER = "Rockypart";

    const FIELD_IMAGE_LINK = "Imagelink";

    const BOX_NUMBER_QUANTITY_PREFIX = "Box";

    const BOX_NUMBER_QUANTITY_POSTFIX = "quantity";

    /**
     * Return box quantity name with box num.
     * @param $num
     * @return string
     */
    public function getBoxQuantityField($num)
    {
        return static::BOX_NUMBER_QUANTITY_PREFIX . $num . static::BOX_NUMBER_QUANTITY_POSTFIX;
    }

    public function getIdentifier()
    {
        return static::FIELD_ID;
    }
}