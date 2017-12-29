<?php


namespace rollun\barcode\Installer;


use Composer\IO\IOInterface;
use Interop\Container\ContainerInterface;
use rollun\barcode\DataStore\Barcode;
use rollun\barcode\DirectoryScanner;
use rollun\datastore\DataStore\Factory\CsvAbstractFactory;
use rollun\datastore\DataStore\Installers\CsvInstaller;
use rollun\installer\Install\InstallerAbstract;

class BarcodeDataStoreInstaller extends InstallerAbstract
{

    /** @var DirectoryScanner */
    protected $directoryScanner;

    public function __construct(ContainerInterface $container, IOInterface $ioComposer)
    {
        parent::__construct($container, $ioComposer);
        $this->directoryScanner = new DirectoryScanner();
    }


    /**
     * install
     * @return array
     */
    public function install()
    {
        $config = [];
        $barcodeFile = $this->directoryScanner->scanDirectory();
        foreach ($barcodeFile as $name => $path) {
            if(!$this->container->has($name)) {
                $config[CsvAbstractFactory::KEY_DATASTORE][$name] = [
                    CsvAbstractFactory::KEY_CLASS => Barcode::class,
                    CsvAbstractFactory::KEY_FILENAME => $path,
                    CsvAbstractFactory::KEY_DELIMITER => ",",
                ];
            }
        }
        return $config;
    }

    /**
     * Clean all installation
     * @return void
     */
    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    /**
     * Return true if install, or false else
     * @return bool
     */
    public function isInstall()
    {
        $barcodeFile = $this->directoryScanner->scanDirectory();
        foreach ($barcodeFile as $name => $path) {
            if(!$this->container->has($name)) {
                return false;
            }
        }
        return true;
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
                return "Gen config for barcodes dataStore";
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