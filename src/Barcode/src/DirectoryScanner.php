<?php


namespace rollun\barcode;

use DirectoryIterator;
use rollun\installer\Command;

class DirectoryScanner
{
    const KEY_BARCODE_STORAGE_DIR = "barcode";

    /**
     * Scan directory for search barcode csv data store.
     * Return array with name of barcode csv file.
     * @return string[]
     */
    public function scanDirectory()
    {
        $barcodeFilePath = [];
        $barcodeStorageDirPath = realpath(Command::getDataDir() . DIRECTORY_SEPARATOR . static::KEY_BARCODE_STORAGE_DIR);
        $dirIterator = new DirectoryIterator($barcodeStorageDirPath);
        foreach ($dirIterator as $dir) {
            if ($dir->isFile() &&
                $dir->isReadable() &&
                $dir->getExtension() == "csv" &&
                preg_match('/([\w]+)Barcode\.csv$/', $dir->getBasename())
            ) {
                $barcodeFilePath[$dir->getBasename(".csv")] = $dir->getPathname();
            }
        }
        return $barcodeFilePath;
    }
}