<?php


namespace rollun\barcode\DataStore\Traits;

use rollun\barcode\DataStore\BarcodeInterface;
use rollun\datastore\Rql\Node\AggregateSelectNode;
use rollun\datastore\Rql\Node\GroupbyNode;
use rollun\datastore\Rql\RqlQuery;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Query;

/**
 * Trait BarcodeTrait
 * @package rollun\barcode\DataStore\Traits
 * @const string FIELD_PARCEL_NUMBER
 * @const string FIELD_ID
 */
trait BarcodeTrait
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

    /**
     * Return items by criteria with mapping, sorting and paging
     *
     * Example:
     * <code>
     *  $query = new \Xiag\Rql\Parser\Query();
     *  $eqNode = new \Xiag\Rql\Parser\Node\ScalarOperator\EqNode(
     *      'fString', 'val2'
     *  );
     *  $query->setQuery($eqNode);
     *  $sortNode = new \Xiag\Rql\Parser\Node\Node\SortNode(['id' => '1']);
     *  $query->setSort($sortNode);
     *  $selectNode = new \Xiag\Rql\Parser\Node\Node\SelectNode(['fFloat']);
     *  $query->setSelect($selectNode);
     *  $limitNode = new \Xiag\Rql\Parser\Node\Node\LimitNode(2, 1);
     *  $query->setLimit($limitNode);
     *  $queryArray = $this->object->query($query);
     * </code>
     *
     *
     * ORDER
     * http://www.simplecoding.org/sortirovka-v-mysql-neskolko-redko-ispolzuemyx-vozmozhnostej.html
     * http://ru.php.net/manual/ru/function.usort.php
     *
     * @param Query $query
     * @return array[] fo items or [] if not any
     */
    abstract public function query(Query $query);

    /**
     * Delete Item by 'id'. Method do nothing if item with that id is absent.
     *
     * @param int|string $id PrimaryKey
     * @return array from elements or null is not support
     */
    abstract public function delete($id);

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
            "count(" . $this->getIdentifier() . ")",
            BarcodeInterface::FIELD_PARCEL_NUMBER
        ]));
        $query->setGroupby(new GroupbyNode([BarcodeInterface::FIELD_PARCEL_NUMBER]));
        $result = $this->query($query);
        $parcelNumbers = [];
        foreach ($result as $item) {
            $parcelNumbers[] = $item[BarcodeInterface::FIELD_PARCEL_NUMBER];
        }
        return array_reverse($parcelNumbers);
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