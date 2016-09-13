<?php

namespace SAPF\Cache;

class CacheItemPoolFile extends CacheItemPoolAbstract
{

    protected $_cacheDir;

    public function __construct($cacheDirectory)
    {
        $this->_cacheDir = $cacheDirectory;
    }

    protected function _clearAllObjectsFromCache()
    {
        \SAPF\Util\Filesystem::delete($this->_cacheDir);
        return true;
    }

    protected function _clearOneObjectFromCache($key)
    {
        $file = \SAPF\Util\Filesystem::file($this->_cacheDir, md5("cache_" . $key));
        return unlink($file);
    }

    protected function _fetchObjectFromCache($key)
    {
        $file = \SAPF\Util\Filesystem::file($this->_cacheDir, md5("cache_" . $key));
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);

            if ($data['expireAt'] != null) {
                $expire = new \DateTime();
                $expire->setTimestamp($data['expireAt']);
            }
            else {
                $expire = null;
            }

            return [true, unserialize($data['data']), $expire];
        }

        return [false, null, null];
    }

    protected function _storeItemInCache(\Psr\Cache\CacheItemInterface $item, $ttl)
    {
        if (!$item->isHit()) {
            return true;
        }

        \SAPF\Util\Filesystem::create($this->_cacheDir); // try to create cache dir
        $file = \SAPF\Util\Filesystem::file($this->_cacheDir, md5("cache_" . $item->getKey()));

        if (file_put_contents($file, json_encode([
                    'data'     => serialize($item->get()),
                    'expireAt' => $ttl > 0 ? time() + $ttl : null,
                ])) === FALSE) {
            return false;
        }

        return true;
    }

}
