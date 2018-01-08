<?php

namespace rollun\barcode\DataStore;

use rollun\barcode\DataStore\Traits\ScansInfoTrait;
use rollun\datastore\DataStore\SerializedDbTable;
use rollun\datastore\TableGateway\TableManagerMysql;

class ScansInfoTable extends SerializedDbTable implements ScansInfoInterface
{
    use ScansInfoTrait;

    const TABLE_NAME = "scans_info";

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
                static::FIELD_PARCEL_NUMBER => [
                    TableManagerMysql::FIELD_TYPE => "Varchar",
                    TableManagerMysql::FIELD_PARAMS => [
                        'length' => 255,
                        'nullable' => false
                    ]
                ],
                static::FIELD_IP => [
                    TableManagerMysql::FIELD_TYPE => "Varchar",
                    TableManagerMysql::FIELD_PARAMS => [
                        'length' => 255,
                        'nullable' => false
                    ]
                ],
                static::FIELD_SCAN_TIME => [
                    TableManagerMysql::FIELD_TYPE => "Integer",
                    TableManagerMysql::FIELD_PARAMS => [
                        'length' => 11,
                        'nullable' => false
                    ]
                ],
            ]
        ];
    }
}