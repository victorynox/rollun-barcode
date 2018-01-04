<?php

namespace rollun\barcode\DataStore;

use rollun\datastore\DataStore\Aspect\AspectAbstract;
use rollun\datastore\DataStore\DataStoreException;
use rollun\utils\IdGenerator;
use rollun\utils\Json\Exception;
use rollun\utils\Json\Serializer;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Query;

/**
 * Class BarcodeAspect
 * Prepare data to save in table
 * @example
 * [
 *      [
 *          "FNSKU" =>
 *          "Rockypart" =>
 *          "Imagelink" =>
 *          "Box1quantity" =>
 *          "Box2quantity" =>
 *          "Box3quantity" =>
 *          "Box4quantity" =>
 *          "Box*quantity" =>
 *          ...
 *      ],
 *      ...
 * ]
 *
 * To
 * [
 *      [
 *          "fnsku" => FNSKU
 *          "part_number" => Rockypart
 *          "image_link" => Imagelink
 *          "parcel_number" => ...
 *          "quantity" => "[
 *                  box1 => Box1quantity
 *                  box2 => Box2quantity
 *                  box3 => Box3quantity
 *                  box4 => Box4quantity
 *              ]"
 *      ],
 *      ...
 * ]
 *
 * @package rollun\barcode\DataStore
 * @property BarcodeInterface $dataStore
 */
class ParcelBarcodeAspect extends AspectAbstract implements BarcodeInterface
{
    const BOX_NUMBER_QUANTITY_PREFIX = "Box";

    const BOX_NUMBER_QUANTITY_POSTFIX = "quantity";

    const GEN_ID_MAX_TRY = 5;

    const DEFAULT_ID_MAX_LENGTH = 10;

    const DEFAULT_ID_CHAR_SET = "QWERTYUIOPADFHKLZXVBNM123456789";

    /**
     * AspectDataStoreAbstract constructor.
     *
     * @param BarcodeInterface $dataStore
     * @param $parcelNumber
     * @param IdGenerator|null $idGenerator
     */
    public function __construct(BarcodeInterface $dataStore, $parcelNumber, IdGenerator $idGenerator = null)
    {
        parent::__construct($dataStore);
        $this->idGenerator = $idGenerator ?: new IdGenerator(static::DEFAULT_ID_MAX_LENGTH, static::DEFAULT_ID_CHAR_SET);
        $this->parcelNumber = $parcelNumber;
    }

    /**
     * Selected parcel number
     * @var string
     */
    protected $parcelNumber;

    /**
     * @var IdGenerator
     */
    protected $idGenerator;

    /**
     * Data with field mapped.
     * @var array
     */
    protected $mappedFields = [
        "FNSKU" => BarcodeInterface::FIELD_FNSKU,
        "Rockypart" => BarcodeInterface::FIELD_PART_NUMBER,
        "Imagelink" => BarcodeInterface::FIELD_IMAGE_LINK,
    ];

    /**
     * Repack box quantity to array with quantity.
     * [
     *   ...
     *  "Box1quantity" =>
     *  "Box2quantity" =>
     *  "Box3quantity" =>
     *  "Box4quantity" =>
     *  "Box*quantity" =>
     *  ...
     * ]
     * to
     * [
     *   [
     *           box1 => Box1quantity
     *           box2 => Box2quantity
     *           box3 => Box3quantity
     *           box4 => Box4quantity
     *   ]
     * ]
     * @param $item
     * @return array
     */
    protected function repackBoxQuantity(array $item)
    {
        $boxQuantity = [];
        $pattern = '/' . static::BOX_NUMBER_QUANTITY_PREFIX . '(?<boxNumber>[\d]+)' . static::BOX_NUMBER_QUANTITY_POSTFIX . '/';
        foreach ($item as $key => $value) {
            if (preg_match($pattern, $key, $match) && !empty($value)) {
                $boxQuantity['box' . $match['boxNumber']] = (int)$value;
            }
        }
        return $boxQuantity;
    }

    /**
     * Repack item field by mappedFields array
     * @param array $item
     * @return array
     */
    protected function repackField(array $item)
    {
        $repackItem = [];
        foreach ($this->mappedFields as $field => $mappedField) {
            if (isset($item[$field])) {
                $repackItem[$mappedField] = $item[$field];
            } elseif (isset($item[$mappedField])) {
                $repackItem[$mappedField] = $item[$mappedField];
            }
        }
        return $repackItem;
    }

    /**
     * Generates an arbitrary length string of cryptographic random bytes
     * @return string
     * @throws DataStoreException
     */
    protected function generateId()
    {
        $tryCount = 0;
        do {
            if($tryCount >= static::GEN_ID_MAX_TRY) {
                throw new DataStoreException("Can't generate id.");
            }
            $id = $this->idGenerator->generate();
            $tryCount++;
        } while($this->has($id));
        return $id;
    }

    /**
     * Repack item
     * @param array $item
     * @return array
     * @throws DataStoreException
     */
    protected function repackItem(array $item)
    {
        $repackItem = $this->repackField($item);
        $repackItem[$this->getIdentifier()] = isset($item[$this->getIdentifier()]) ?
            $item[$this->getIdentifier()] : $this->generateId();
        $repackItem[BarcodeInterface::FIELD_PARCEL_NUMBER] = $this->parcelNumber;
        try {
            $repackItem[BarcodeInterface::FIELD_QUANTITY_DATA] = Serializer::jsonSerialize($this->repackBoxQuantity($item));
        } catch (Exception $e) {
            throw new DataStoreException("Can't serialize box quantity data.", $e->getCode(), $e);
        }
        return $repackItem;
    }

    /**
     * Not work in non-flat datastore.
     * Check if data is multiply or is single item.
     * @param array $itemData
     * @return boolean
     */
    protected function isDataMultipleData(array $itemData)
    {
        return (boolean)(count($itemData, COUNT_RECURSIVE) - count($itemData));
    }

    /**
     * Prepare data before create
     * @param $itemData
     * @param bool $rewriteIfExist
     * @return array
     * @throws DataStoreException
     */
    protected function preCreate($itemData, $rewriteIfExist = false)
    {
        $repackItemData = [];
        if ($this->isDataMultipleData($itemData)) {
            foreach ($itemData as $item) {
                $repackItemData = $this->repackItem($item);
            }
        } else {
            $repackItemData = $this->repackItem($itemData);
        }
        return $repackItemData;
    }

    /**
     * Prepare data before update
     * @param $itemData
     * @param bool $createIfAbsent
     * @return array
     * @throws DataStoreException
     */
    protected function preUpdate($itemData, $createIfAbsent = false)
    {
        $repackItemData = [];
        if ($this->isDataMultipleData($itemData)) {
            foreach ($itemData as $item) {
                $repackItemData = $this->repackItem($item);
            }
        } else {
            $repackItemData = $this->repackItem($itemData);
        }
        return $repackItemData;
    }

    /**
     * Add concrete parcel number to query
     * @param Query $query
     * @return Query
     */
    protected function preQuery(Query $query)
    {
        $queryNodes = $query->getQuery();
        if (isset($queryNodes)) {
            $queryNodes = new AndNode([new EqNode(BarcodeInterface::FIELD_PARCEL_NUMBER, $this->parcelNumber), $queryNodes]);
        } else {
            $queryNodes = new EqNode(BarcodeInterface::FIELD_PARCEL_NUMBER, $this->parcelNumber);
        }
        $query->setQuery($queryNodes);
        return $query;
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
        return $this->dataStore->getParcelNumbers();
    }

    /**
     * Remove all item which contained in selected parcel.
     * @param $parcelNumber
     * @return void
     */
    public function deleteParcel($parcelNumber)
    {
        $this->dataStore->deleteParcel($parcelNumber);
    }

    /**
     * Delete all item in parcel
     * @return int|mixed|void
     */
    public function deleteAll()
    {
        $this->deleteParcel($this->parcelNumber);
    }

    /**
     * Delete item from a parcel
     * @param int|string $id
     * @return array|mixed|void
     */
    public function delete($id)
    {
        if ($this->has($id)) {
            $this->dataStore->delete($id);
        }
    }

    /**
     * Return item only if parcel contained he
     * @param int|string $id
     * @return array|null
     */
    public function read($id)
    {
        $query = new Query();
        $query->setQuery(new AndNode([
            new EqNode(static::FIELD_ID, $id),
            new EqNode(static::FIELD_PARCEL_NUMBER, $this->parcelNumber),
        ]));
        $query->setLimit(new LimitNode(1));
        $result = $this->dataStore->query($query);
        if (!empty($result)) {
            return $result[0];
        }
        return null;
    }

    /**
     * Check if  item has in parcel
     * @param int|string $id
     * @return bool
     */
    public function has($id)
    {
        return $this->read($id) != null;
    }


    /**
     * @param $parcelNumber
     * @return boolean
     */
    public function hasParcel($parcelNumber)
    {
        return $this->dataStore->hasParcel($parcelNumber);
    }
}