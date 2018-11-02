<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Service\Import\Products\A\Save\Media\A;

/**
 * Check cache directory for the image (SKU named) and download image using URL if necessary.
 * Then place cached image into catalog gallery (/pub/media/catalog/product/...).
 */
class Download
{
    /** @var \Magento\Framework\Filter\TranslitUrl */
    private $filterTranslit;
    /** @var \Em34\App\Helper\Paths */
    private $hlpPaths;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filter\TranslitUrl $filterTranslit,
        \Em34\App\Helper\Paths $hlpPaths
    ) {
        $this->logger = $logger;
        $this->hlpPaths = $hlpPaths;
        $this->filterTranslit = $filterTranslit;
    }

    public function exec($sku, $url)
    {
        $result = null;
        $normalized = $this->normalizeSku($sku);
        $pathPrefix = $this->hlpPaths->getPathPrefixForName($normalized);
        /* look up for image file in catalog media */
        $dirMediaPub = $this->hlpPaths->getDirPubMediaCatalog();
        $pathToFileMedia = $dirMediaPub . $pathPrefix;
        $this->makeDir($pathToFileMedia);
        $pattern = $pathToFileMedia . DIRECTORY_SEPARATOR . $normalized . '.*';
        $found = glob($pattern);
        if (!$found) {
            /* look up for image file in images cache */
            $dirMediaCache = $this->hlpPaths->getDirTmpMediaCache();
            $pathToFileCache = $dirMediaCache . $pathPrefix;
            $pattern = $pathToFileCache . DIRECTORY_SEPARATOR . $normalized . '.*';
            $found = glob($pattern);
            if (!$found) {
                /* download file using URL */
                $this->logger->info("Downloading image for product #$sku from '$url'...");
                $content = file_get_contents($url);
                $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $fileInfo->buffer($content);
                switch ($mimeType) {
                    case 'image/png':
                        $ext = 'png';
                        break;
                    case 'image/jpeg':
                        $ext = 'jpg';
                        break;
                    default:
                        $this->logger->error("Unknown MIME type: $mimeType.");
                        $ext = 'image';
                }
                $filename = $normalized . '.' . $ext;
                $fullPathCache = $pathToFileCache . DIRECTORY_SEPARATOR . $filename;
                /* put content to the cache dir */
                $this->makeDir($pathToFileCache);
                $saved = file_put_contents($fullPathCache, $content);
                if ($saved) {
                    /* put content to the catalog media dir */
                    $fullPathMedia = $pathToFileMedia . DIRECTORY_SEPARATOR . $filename;
                    copy($fullPathCache, $fullPathMedia);
                    $result = $pathPrefix . DIRECTORY_SEPARATOR . $filename;
                }
            } else {
                /* one only file should be found */
                $fullPathCache = reset($found);
                /* put content to the catalog media dir */
                $filename = pathinfo($fullPathCache, PATHINFO_BASENAME);
                $fullPathMedia = $pathToFileMedia . DIRECTORY_SEPARATOR . $filename;
                copy($fullPathCache, $fullPathMedia);
                $result = $pathPrefix . DIRECTORY_SEPARATOR . $filename;
            }
        } else {
            /* one only file should be found */
            $fullPathMedia = reset($found);
            $filename = pathinfo($fullPathMedia, PATHINFO_BASENAME);
            $result = $pathPrefix . DIRECTORY_SEPARATOR . $filename;
        }
        return $result;
    }

    /**
     * Create directory if not exist.
     *
     * @param string $fullPath
     */
    private function makeDir($fullPath)
    {
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0770, true);
        }
    }

    private function normalizeSku($sku)
    {
        $result = trim($sku);
        $result = mb_strtolower($result);
        $result = $this->filterTranslit->filter($result);
        $result = str_replace(' ', '_', $result);
        $result = str_replace('\\', '_', $result);
        $result = str_replace('/', '_', $result);
        $result = str_replace('-', '_', $result);
        return $result;
    }
}