<?php


namespace rollun\barcode\Action\Admin;

use Psr\Http\Message\ServerRequestInterface;
use rollun\barcode\DataStore\BarcodeInterface as BarcodeDataStoreInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Zend\Expressive\Exception\InvalidMiddlewareException;
use Zend\Expressive\Helper\UrlHelper;

abstract class ParcelAbstract implements MiddlewareInterface
{
    const KEY_ATTRIBUTE_PARCEL_NUMBER = "parcel_number";

    /**
     * @var BarcodeDataStoreInterface
     */
    protected $barcodeDataStore;

    /** @var UrlHelper */
    protected $urlHelper;

    /**
     * ViewParcel constructor.
     * @param BarcodeDataStoreInterface $barcodeDataStore
     * @param UrlHelper $urlHelper
     */
    public function __construct(BarcodeDataStoreInterface $barcodeDataStore, UrlHelper $urlHelper)
    {
        $this->barcodeDataStore = $barcodeDataStore;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param ServerRequestInterface $request
     * @throws InvalidMiddlewareException
     * @return mixed
     */
    protected function resolveParcelNumber(ServerRequestInterface $request)
    {
        $parcelNumber = $request->getAttribute(static::KEY_ATTRIBUTE_PARCEL_NUMBER);
        if (is_null($parcelNumber)) {
            throw new InvalidMiddlewareException("Not set " . static::KEY_ATTRIBUTE_PARCEL_NUMBER . " attribute.");
        }
        return $parcelNumber;
    }
}