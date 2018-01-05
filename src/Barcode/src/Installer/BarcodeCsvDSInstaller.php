<?php


namespace rollun\barcode\Installer;


use Composer\IO\IOInterface;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\barcode\DataStore\BarcodeCsv;
use rollun\datastore\DataStore\Factory\CsvAbstractFactory;
use rollun\datastore\DataStore\Installers\CsvInstaller;
use rollun\installer\Command;
use rollun\installer\Install\InstallerAbstract;

class BarcodeCsvDSInstaller extends InstallerAbstract
{
    const STORAGE_DIR = "barcode";

    const CSV_DATASTORE = "rockyBarcode.csv";

    const HEADER = "id,fnsku,part_number,parcel_number,image_link,quantity";

    public function __construct(ContainerInterface $container, IOInterface $ioComposer)
    {
        parent::__construct($container, $ioComposer);
    }

    /**
     * Return path to csv storage file
     * @return string
     */
    protected function getStoragePath()
    {
        return Command::getDataDir() . DIRECTORY_SEPARATOR . self::STORAGE_DIR . DIRECTORY_SEPARATOR . self::CSV_DATASTORE;
    }

    /**
     * Create storage file and write csv header.
     */
    protected function initStorageFile()
    {
        $storageFilePath = $this->getStoragePath();
        if(!file_exists($storageFilePath)) {
            touch($storageFilePath);
            fwrite(fopen($storageFilePath, "w"), static::HEADER . "\n");
        }
    }

    /**
     * install
     * @return array
     */
    public function install()
    {
        $path = $this->getStoragePath();
        $this->initStorageFile();

        $config = [];
        $config['dependencies']['aliases']["BarcodeCsv"] = BarcodeCsv::class;
        $config[CsvAbstractFactory::KEY_DATASTORE][BarcodeCsv::class] = [
            CsvAbstractFactory::KEY_CLASS => BarcodeCsv::class,
            CsvAbstractFactory::KEY_FILENAME => $path,
            CsvAbstractFactory::KEY_DELIMITER => ",",
        ];
        return $config;
    }

    /**
     * Clean all installation
     * @return void
     */
    public function uninstall()
    {
        $path = $this->getStoragePath();
        if(file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Return true if install, or false else
     * @return bool
     */
    public function isInstall()
    {
        try {
            $config = $this->container->get("config");
        } catch (NotFoundExceptionInterface $e) {
            return false;
        } catch (ContainerExceptionInterface $e) {
            return false;
        }
        return (
            isset($config[CsvAbstractFactory::KEY_DATASTORE][BarcodeCsv::class]) &&
            file_exists($this->getStoragePath())
        );
    }

    /**
     * Return string with description of installable functional.
     * @param string $lang ; set select language for description getted.
     * @return string
     */
    public function getDescription($lang = "en")
    {
        switch ($lang) {
            case "en":
                return "Gen config and barcodes dataStore";
            case "ru":
                return "Генерирует файл конфига для barcode хранилишь.";
            default:
                return "Hasn't description for selected language.";
        }
    }

    public function getDependencyInstallers()
    {
        return [
            CsvInstaller::class
        ];
    }


}