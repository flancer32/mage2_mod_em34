<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Cli\Import;

use Em34\App\Service\Import\Products\Request as AReqImport;
use Em34\App\Service\Replicate\Product\Save\Request as ARequest;
use Em34\App\Service\Replicate\Product\Save\Request\Attribute as DAttr;
use Em34\App\Service\Replicate\Product\Save\Response as AResponse;

/**
 * Console command to import products from JSON.
 */
class Products
    extends \Symfony\Component\Console\Command\Command
{
    private const BUNCH_SIZE = 100;
    private const DESC = 'Import products from JSON (catalog initialization).';
    private const NAME = 'em34:import:prod';
    private const OPT_ALL_DEFAULT = 'no';
    private const OPT_ALL_NAME = 'all';
    private const OPT_ALL_SHORTCUT = 'a';
    private const OPT_LIMIT_DEFAULT = 100;
    private const OPT_LIMIT_NAME = 'limit';
    private const OPT_LIMIT_SHORTCUT = 'l';
    private const OPT_PATH_NAME = 'path';
    private const OPT_PATH_SHORTCUT = 'p';

    /** @var \Magento\Framework\App\State */
    private $appState;
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $manObj;
    private $srvImportProd;
    /** @var \Em34\App\Service\Replicate\Product\Save */
    private $srvProdSave;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\State $appState,
        \Em34\App\Service\Import\Products $srvImportProd,
        \Em34\App\Service\Replicate\Product\Save $srvProdSave
    ) {
        parent::__construct(self::NAME);
        /* Symfony related config is called from parent constructor */
        $this->setDescription(self::DESC);
        /* own properties */
        $this->manObj = $manObj;
        $this->appState = $appState;
        $this->srvImportProd = $srvImportProd;
        $this->srvProdSave = $srvProdSave;
        /* add command options */
        $this->addOption(
            self::OPT_ALL_NAME,
            self::OPT_ALL_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            "Set 'yes' to import all lines, 'limit' option is ignored in this case (default: no).",
            self::OPT_ALL_DEFAULT
        );
        $this->addOption(
            self::OPT_LIMIT_NAME,
            self::OPT_LIMIT_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            "Limit number of records for importing (default: 100).",
            self::OPT_LIMIT_DEFAULT
        );
        $this->addOption(
            self::OPT_PATH_NAME,
            self::OPT_PATH_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED,
            "Full path to JSON file with data to import into Magneto Catalog."
        );
    }

    /**
     * Sets area code to start a adminhtml session and configure Object Manager.
     */
    private function checkAreaCode()
    {
        try {
            /* area code should be set only once */
            $this->appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            /* exception will be thrown if no area code is set */
            $areaCode = \Magento\Framework\App\Area::AREA_GLOBAL;
            $this->appState->setAreaCode($areaCode);
            /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
            $configLoader = $this->manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
            $config = $configLoader->load($areaCode);
            $this->manObj->configure($config);
        }
    }

    private function convertToRequest($item)
    {
        $result = new ARequest();
//        $attrSource = isset($item->additionalattributes) ? $item->additionalattributes : '';
//        $attrParsed = $this->parseAttrs($attrSource);
        $result->attributes = [];
        $result->description = isset($item->description) ? $item->description : '';
        $result->descShort = isset($item->short_description) ? $item->short_description : '';
        $result->name = isset($item->name) ? $item->name : '';
        $result->price = isset($item->price) ? $item->price : '';
        $result->qty = isset($item->qty) ? $item->qty : '';
        $result->sku = $item->sku;
        $result->status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
        $result->urlkey = isset($item->urlkey) ? $item->urlkey : '';
        $result->weight = isset($item->weight) ? $item->weight : '';
        return $result;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /** define local working data */
        $output->writeln("Command '{$this->getName()}' is started.");
        $dateStarted = date('Y-m-d H:i:s');
        $all = (string)$input->getOption(self::OPT_ALL_NAME);
        $limit = (int)$input->getOption(self::OPT_LIMIT_NAME);
        $path = (string)$input->getOption(self::OPT_PATH_NAME);
        $msg = 'Arguments: ' . self::OPT_ALL_NAME . "=$all; ";
        $msg .= self::OPT_LIMIT_NAME . "=$limit; ";
        $msg .= self::OPT_PATH_NAME . "=$path; ";
        $output->writeln($msg);

        /** perform operation */
        $this->checkAreaCode();
        /* read JSON */
        $json = $this->readJson($path);
        $total = count($json);
        $output->writeln("Total $total records read from input JSON.");
        /* define number of lines to import */
        if ($all == self::OPT_ALL_DEFAULT) {
            if ($limit <= 0) {
                $limit = self::OPT_LIMIT_DEFAULT;
            }
            $json = array_slice($json, 0, $limit);
        }

        /* process JSON data */
        $bunchSize = self::BUNCH_SIZE;
        $total = count($json);
        $output->writeln("Importing $total records using $bunchSize rows bunches (see \${MAGE}/var/log/system.log to trace)...");
        $req = $this->getImportRequest($json, 100);
        $resp = $this->srvImportProd->exec($req);
//        foreach ($json as $one) {
//            /** @var ARequest $req */
//            $req = $this->convertToRequest($one);
        /** @var AResponse $resp (is not used yet) */
//            $resp = $this->srvProdSave->exec($req);
//            $mageId = $resp->mageId;
//            $name = $resp->name;
//            $sku = $resp->sku;
//            $output->writeln("\t$mageId:\t$sku - $name.");
//        }

        /** compose result */
        $dateCompleted = date('Y-m-d H:i:s');
        $output->writeln("Import is started at '$dateStarted' and is completed at '$dateCompleted'.");
        $output->writeln("Command '{$this->getName()}' is executed.");
    }

    private function getCountryCode($name)
    {
        $nameBoo = trim(mb_strtolower($name));
        switch ($nameBoo) {
            case '':
                $result = null;
                break;
            case 'китай':
                $result = 'CN';
                break;
            case 'польша':
                $result = 'PL';
                break;
            case 'россия':
                $result = 'RU';
                break;
            case 'турция':
                $result = 'TR';
                break;
            case 'украина':
                $result = 'UA';
                break;
            case 'япония':
                $result = 'JP';
                break;
            default:
                $result = null;
        }
        return $result;
    }

    private function getImportRequest($json, $bunchSize)
    {
        $result = new AReqImport();
        $result->bunchSize = $bunchSize;
        $items = [];
        foreach ($json as $one) {
            /** define local working data */
            if (property_exists($one, 'sku')) {
                $sku = $one->sku;
                $name = $one->name ?? '';
                $description = $one->description ?? '';
                $price = $one->price ?? 0;
                $weight = $one->weight ?? 0;
                $qty = $one->qty ?? 0;
                $imageUrl = $one->imageurl ?? '';
                $country = $one->countryofmanufacture ?? '';
                $country = $this->getCountryCode($country);

                /** compose result */
                $prod = new \Em34\App\Service\Import\Products\Request\Item\Product();
                $prod->country = $country;
                $prod->description = $description;
                $prod->imageUrl = $imageUrl;
                $prod->name = $name;
                $prod->price = $price;
                $prod->qty = $qty;
                $prod->sku = $sku;
                $prod->weight = $weight;

                $item = new \Em34\App\Service\Import\Products\Request\Item();
                $item->product = $prod;
                $items[] = $item;
            }
        }
        $result->items = $items;
        return $result;
    }

    /**
     * Product attributes parsing.
     *
     * @param string $source
     * @return DAttr[]
     */
    private function parseAttrs($source)
    {
        $result = [];
        $attrs = explode(',', $source);
        foreach ($attrs as $attr) {
            $parts = explode('=', $attr);
            $one = new DAttr();
            $one->code = $parts[0];
            $one->value = $parts[1];
        }
        return $result;
    }

    /**
     * @param string $path full path to JSON file with import data.
     * @return array parsed data as associative array.
     */
    private function readJson($path)
    {
        $content = file_get_contents($path);
        $result = json_decode($content);
        return $result;
    }
}