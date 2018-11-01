<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Em34\App\Helper;

/**
 * Compose paths to filesystem objects.
 */
class Paths
{
    private $cacheDirPubMediaCatalog;
    private $cacheDirTmpMediaCache;

    public function getDirPubMediaCatalog()
    {
        if (is_null($this->cacheDirPubMediaCatalog)) {
            $relative = BP . '/pub/media/catalog/product';
            $absolute = realpath($relative);
            if (is_dir($absolute)) {
                $this->cacheDirPubMediaCatalog = $absolute;
            } else {
                $created = mkdir($relative, 0770, true);
                if ($created) {
                    $absolute = realpath($relative);
                    $this->cacheDirPubMediaCatalog = $absolute;
                } else {
                    throw  new \Exception("Cannot create directory: '$absolute'.");
                }
            }
        }
        return $this->cacheDirPubMediaCatalog;

    }

    public function getDirTmpMediaCache()
    {
        if (is_null($this->cacheDirTmpMediaCache)) {
            $relative = BP . '/../tmp/import/img';
            $absolute = realpath($relative);
            if (is_dir($absolute)) {
                $this->cacheDirTmpMediaCache = $absolute;
            } else {
                $created = mkdir($relative, 0770, true);
                if ($created) {
                    $absolute = realpath($relative);
                    $this->cacheDirTmpMediaCache = $absolute;
                } else {
                    throw  new \Exception("Cannot create directory: '$absolute'.");
                }
            }
        }
        return $this->cacheDirTmpMediaCache;

    }

    /**
     * Return '/a/b' for 'aBcDeF' (to place multiple files in different catalogs).
     *
     * @param string $name
     * @return string
     */
    public function getPathPrefixForName($name)
    {
        $norm = trim(strtolower($name));
        $a = $norm[0];
        $b = $norm[1];
        $result = DIRECTORY_SEPARATOR . $a . DIRECTORY_SEPARATOR . $b;
        return $result;
    }
}