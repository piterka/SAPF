<?php

namespace SAPF\Repository;

class RepositoryManager
{

    protected $_db;
    protected $_registered;
    protected $_container = null;

    public function __construct(\SAPF\Database\SQLDatabase $db)
    {
        $this->_db         = $db;
        $this->_registered = [];
    }

    public function setContainer(Container $container)
    {
        $this->_container = $container;
        return $this;
    }

    public function getContainer()
    {
        return $this->_container;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Return SQLDatabase
     * @return \SAPF\Database\SQLDatabase
     */
    public function getDb()
    {
        return $this->_db;
    }

    /**
     * Register Repository
     * @param \SAPF\Repository\RepositoryAbstract $repository
     * @return \SAPF\Repository\RepositoryManager
     */
    public function register(RepositoryAbstract $repository)
    {
        $repository->setRepositoryManager($this);
        $this->_registered[strtolower($repository->getName())] = $repository;
        return $this;
    }

    /**
     * Return registered Repositories
     * @return array
     */
    public function getAll()
    {
        return $this->_registered;
    }

    /**
     * Get repository by name
     * @param string $name
     * @return \SAPF\Repository\RepositoryAbstract
     */
    public function get($name)
    {
        if (!array_key_exists(strtolower($name), $this->_registered)) {
            return null;
        }
        return $this->_registered[strtolower($name)];
    }

}
