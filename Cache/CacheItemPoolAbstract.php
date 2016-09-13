<?php

namespace SAPF\Cache;

abstract class CacheItemPoolAbstract implements \Psr\Cache\CacheItemPoolInterface
{

    protected $_deferred = [];

    /**
     * Make sure to commit before we destruct.
     */
    public function __destruct()
    {
        $this->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        $this->_validateKey($key);
        if (isset($this->_deferred[$key])) {
            $item = $this->_deferred[$key];
            return is_object($item) ? clone $item : $item;
        }
        $func = function () use ($key) {
            return $this->_fetchObjectFromCache($key);
        };
        return new CacheItem($key, $func);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        return $this->getItem($key)->isHit();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        // Clear the deferred items
        $this->_deferred = [];
        return $this->_clearAllObjectsFromCache();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        return $this->deleteItems([$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        $deleted = true;
        foreach ($keys as $key) {
            $this->_validateKey($key);
            // Delete form deferred
            unset($this->_deferred[$key]);
            if (!$this->_clearOneObjectFromCache($key)) {
                $deleted = false;
            }
        }
        return $deleted;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Psr\Cache\CacheItemInterface $item)
    {
        $timeToLive = null;
        if ($item instanceof CacheItem) {
            if (null !== $expirationDate = $item->getExpirationDate()) {
                $timeToLive = $expirationDate->getTimestamp() - time();
            }
        }
        return $this->_storeItemInCache($item, $timeToLive);
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(\Psr\Cache\CacheItemInterface $item)
    {
        $this->_deferred[$item->getKey()] = $item;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $saved = true;
        foreach ($this->_deferred as $item) {
            if (!$this->save($item)) {
                $saved = false;
            }
        }
        $this->_deferred = [];
        return $saved;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     */
    protected function _validateKey($key)
    {
        if (!is_string($key)) {
            throw new \SAPF\Cache\CacheException('Cache key must be string, "' . gettype($key) . '" given');
        }
        if (preg_match('|[\{\}\(\)/\\\@\:]|', $key)) {
            throw new \SAPF\Cache\CacheException('Invalid key: "' . $key . '". The key contains one or more characters reserved for future extension: {}()/\@:');
        }
    }

    /**
     * @param CacheItemInterface $item
     * @param int|null           $ttl  seconds from now
     *
     * @return bool true if saved
     */
    abstract protected function _storeItemInCache(\Psr\Cache\CacheItemInterface $item, $ttl);

    /**
     * Fetch an object from the cache implementation.
     *
     * @param string $key
     *
     * @return array with [isHit, value]
     */
    abstract protected function _fetchObjectFromCache($key);

    /**
     * Clear all objects from cache.
     *
     * @return bool false if error
     */
    abstract protected function _clearAllObjectsFromCache();

    /**
     * Remove one object from cache.
     *
     * @param string $key
     *
     * @return bool
     */
    abstract protected function _clearOneObjectFromCache($key);
}
