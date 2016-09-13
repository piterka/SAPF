<?php

namespace SAPF\DI;

class Container implements ContainerInterface
{

    protected $_entriesDefinitions;

    /**
     * @var \SAPF\DI\ContainerInterface
     */
    protected $_fallbackContainer;

    /**
     * Gets fallback container
     * @return \SAPF\DI\ContainerInterface
     */
    public function getFallbackContainer()
    {
        return $this->_fallbackContainer;
    }

    /**
     * Sets fallback container
     * @param \SAPF\DI\ContainerInterface $fallbackContainer
     * @return \SAPF\DI\Container
     */
    public function setFallbackContainer(\SAPF\DI\ContainerInterface $fallbackContainer)
    {
        $this->_fallbackContainer = $fallbackContainer;
        return $this;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for this identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->{$id};
        }

        throw new Exception\NotFoundExceptionInterface("Entry \"$id\" is not defined!");
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        return isset($this->{$id});
    }

    /**
     * Define entry in container
     * @param string $id Unique entry key 
     * @param \Closure $definition
     * @return \SAPF\DI\Container
     */
    public function define($id, $definition)
    {
        $this->_entriesDefinitions[$id] = $definition;
        unset($this->{$id});
        return $this;
    }

    /**
     * Remove custom definition of entry from container
     * @param string $id Unique entry key 
     * @return \SAPF\DI\Container
     */
    public function undefine($id)
    {
        unset($this->_entriesDefinitions[$id]);
        unset($this->{$id});
        return $this;
    }

    // ArrayAccess
    public function offsetExists($id)
    {
        return $this->has($id);
    }

    public function offsetGet($id)
    {
        return $this->get($id);
    }

    public function offsetSet($id, $definition)
    {
        $this->define($id, $definition);
        return $this;
    }

    public function offsetUnset($id)
    {
        $this->undefine($id);
    }

    // magic
    public function __get($name)
    {
        if (isset($this->_entriesDefinitions[$name])) {
            $definition = $this->_entriesDefinitions[$name];
            if ($definition instanceof \Closure) {
                return call_user_func($definition, $this, $name);
            }
            else {
                return $definition;
            }
        }

        if (method_exists($this, "_{$name}")) {
            return $this->{"_{$name}"}();
        }

        if ($this->_fallbackContainer && $this->_fallbackContainer->has($name)) {
            return $this->_fallbackContainer->get($name);
        }
    }

    public function __isset($name)
    {
        if (isset($this->_entriesDefinitions[$name])) {
            return true;
        }
        if ($this->_fallbackContainer && $this->_fallbackContainer->has($name)) {
            return true;
        }
        return method_exists($this, "_{$name}");
    }

    public function __call($name, $arguments)
    {
        if ($this->_fallbackContainer) {
            return call_user_func_array(array($this->_fallbackContainer, $name), $arguments);
        }
    }

}
