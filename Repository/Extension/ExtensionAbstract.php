<?php

namespace SAPF\Repository\Extension;

class ExtensionAbstract implements ExtensionInterface
{

    protected $_repository;
    protected $_name;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * Set repository to extension
     * @param \SAPF\Repository\RepositoryAbstract $repository
     * @return \SAPF\Repository\Extension\ExtensionAbstract
     */
    public function setRepository(\SAPF\Repository\RepositoryAbstract $repository)
    {
        $this->_repository = $repository;
        return $this;
    }

    /**
     * Return RepositoryManager
     * @return \SAPF\Repository\RepositoryManager
     */
    public function getRepositoryManager()
    {
        return $this->_repository->getRepositoryManager();
    }

    /**
     * Get repository
     * @return \SAPF\Repository\RepositoryAbstract
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * Return database connection
     * @return \SAPF\Database\SQLDatabase
     */
    public function getDb()
    {
        return $this->getRepository()->getDb();
    }

    /**
     * Return extension name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    //

    public function fetcher(array &$param, \SAPF\Database\Model &$model)
    {
        
    }

    public function remove(array $entity)
    {
        
    }

    public function save(array $entity)
    {
        
    }

    public function tuple2entity(array $param, array $request, array &$tuple)
    {
        
    }

    public function tuples2entities(array $param, array $request, array &$tuples)
    {
        
    }

}
