<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Cli\Import;

use Em34\App\Service\Replicate\Product\Save\Request as ARequest;
use Em34\App\Service\Replicate\Product\Save\Response as AResponse;

/**
 * Console command to import products from JSON.
 */
class Products
    extends \Symfony\Component\Console\Command\Command
{
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
    /** @var \Em34\App\Service\Replicate\Product\Save */
    private $srvProdSave;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\State $appState,
        \Em34\App\Service\Replicate\Product\Save $srvProdSave
    ) {
        parent::__construct(self::NAME);
        /* Symfony related config is called from parent constructor */
        $this->setDescription(self::DESC);
        /* own properties */
        $this->manObj = $manObj;
        $this->appState = $appState;
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
        $result->attributes = [];
        $result->descShort = isset($item->short_description) ? $item->short_description : '';
        $result->description = isset($item->description) ? $item->description : '';
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
        /* define number of lines to import */
        if ($all == self::OPT_ALL_DEFAULT) {
            if ($limit <= 0) {
                $limit = self::OPT_LIMIT_DEFAULT;
            }
            $json = array_slice($json, 0, $limit);
        }

        /* process JSON data */
        foreach ($json as $one) {
            /** @var ARequest $req */
            $req = $this->convertToRequest($one);
            /** @var AResponse $resp (is not used yet) */
            $resp = $this->srvProdSave->exec($req);
            $mageId = $resp->mageId;
            $name = $resp->name;
            $sku = $resp->sku;
            $output->writeln("\t$mageId:\t$sku - $name.");
        }

        /** compose result */
        $output->writeln("Command '{$this->getName()}' is executed.");
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