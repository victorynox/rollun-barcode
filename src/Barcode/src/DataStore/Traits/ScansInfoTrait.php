<?php

namespace rollun\barcode\DataStore\Traits;

use Xiag\Rql\Parser\Query;

/**
 * Trait ScansInfoTrait
 * @package rollun\barcode\DataStore\Traits
 * @const FIELD_ID
 */
trait ScansInfoTrait
{
    /**
     * Return primary key identifier
     *
     * Return "id" by default
     *
     * @see DEF_ID
     * @return string "id" by default
     */
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return static::FIELD_ID;
    }
}