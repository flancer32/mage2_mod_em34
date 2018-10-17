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

    const DESC = 'Import products from JSON (catalog initialization).';
    const NAME = 'em34:import:prod';
    /** TODO: add CLI parameter for input file name */
    const TMP = '/home/alex/Dropbox/work/prj/em34/import_20181017.json';
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

        /** perform operation */
        $this->checkAreaCode();
        $json = $this->readJson();
        $json = array_slice($json, 0, 100);
        foreach ($json as $one) {
            /** @var ARequest $req */
            $req = $this->convertToRequest($one);
            /** @var AResponse $resp */
            $resp = $this->srvProdSave->exec($req);
        }

        /** compose result */

        $output->writeln("Command '{$this->getName()}' is executed.");
    }

    private function readJson()
    {
        $content = file_get_contents(self::TMP);
        $result = json_decode($content);
        return $result;
    }
}