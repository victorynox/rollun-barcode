<?php


namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\SerializedDbTable;
use rollun\datastore\Rql\Node\AggregateSelectNode;
use rollun\datastore\Rql\Node\GroupbyNode;
use rollun\datastore\Rql\RqlQuery;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Query;

class BarcodeTable extends SerializedDbTable implements BarcodeInterface
{
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return static::FIELD_ID;
    }

    /**
     * Return array with all different parcel name.
     * [
     *    "12ms931s",
     *    "1656m12931s",
     *    "16Av56m123",
     * ]
     * @return array
     */
    public function getParcelNumbers()
    {
        $query = new RqlQuery();
        $query->setSelect(new AggregateSelectNode([
            "count(".$this->getIdentifier().")",
            BarcodeInterface::FIELD_PARCEL_NUMBER
        ]));
        $query->setGroupby(new GroupbyNode([BarcodeInterface::FIELD_PARCEL_NUMBER]));
        $result = $this->query($query);
        $parcelNumbers = [];
        foreach ($result as $item) {
            $parcelNumbers[] = $item[BarcodeInterface::FIELD_PARCEL_NUMBER];
        }
        return $parcelNumbers;
    }

    /**
     * Remove all item which contained in selected parcel.
     * @param $parcelNumber
     * @return void
     */
    public function deleteParcel($parcelNumber)
    {
        $query = new Query();
        $query->setQuery(new EqNode(static::FIELD_PARCEL_NUMBER, $parcelNumber));
        $result = $this->query($query);
        foreach ($result as $item) {
            $this->delete($item[$this->getIdentifier()]);
        }
    }

    /**
     * @param $parcelNumber
     * @return boolean
     */
    public function hasParcel($parcelNumber)
    {
        $query = new Query();
        $query->setQuery(new EqNode(static::FIELD_PARCEL_NUMBER, $parcelNumber));
        $result = $this->query($query);
        return !empty($result);
    }
}