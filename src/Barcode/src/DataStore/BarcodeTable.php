<?php


namespace rollun\barcode\DataStore;

use rollun\barcode\DataStore\Traits\BarcodeTrait;
use rollun\datastore\DataStore\SerializedDbTable;
use rollun\datastore\TableGateway\TableManagerMysql;

class BarcodeTable extends SerializedDbTable implements BarcodeInterface
{
    use BarcodeTrait;

    const TABLE_NAME = "barcode";

    /**
     * Return Db table config
     * @return array
     */
    public static function getTableConfig()
    {
        return [
            static::TABLE_NAME => [
                static::FIELD_ID => [
                    TableManagerMysql::FIELD_TYPE => "Varchar",
                    TableManagerMysql::PRIMARY_KEY => true,
                    TableManagerMysql::FIELD_PARAMS => [
                        'length' => 255,
                        'nullable' => false
                    ]
                ],
                static::FIELD_FNSKU => [
                    TableManagerMysql::FIELD_TYPE => "Varchar",
                    TableManagerMysql::FIELD_PARAMS => [
                        'length' => 255,
                        'nullable' => false
                    ]
                ],
                static::FIELD_PART_NUMBER => [
                    TableManagerMysql::FIELD_TYPE => "Varchar",
                    TableManagerMysql::FIELD_PARAMS => [
                        'length' => 255,
                        'nullable' => false
                    ]
                ],
                static::FIELD_PARCEL_NUMBER => [
                    TableManagerMysql::FIELD_TYPE => "Varchar",
                    TableManagerMysql::FIELD_PARAMS => [
                        'length' => 255,
                        'nullable' => false
                    ]
                ],
                static::FIELD_IMAGE_LINK => [
                    TableManagerMysql::FIELD_TYPE => "Text",
                    TableManagerMysql::FIELD_PARAMS => [
                        'length' => 65535,
                        'nullable' => true
                    ]
                ],
                static::FIELD_QUANTITY_DATA => [
                    TableManagerMysql::FIELD_TYPE => "Text",
                    TableManagerMysql::FIELD_PARAMS => [
                        'nullable' => false,
                        'length' => 65535,
                    ]
                ],
            ]
        ];
    }
}