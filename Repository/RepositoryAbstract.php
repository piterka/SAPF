<?php

namespace SAPF\Repository;

abstract class RepositoryAbstract implements RepositoryInterface
{

    protected $_fields;
    protected $_name;
    protected $_repositoryManager;
    protected $_extensions = [];

    public function __construct($name, $fields)
    {
        $this->_name = $name;
        if (!in_array($this->getPrimaryKeyName(), $fields)) {
            $fields[] = $this->getPrimaryKeyName();
        }
        $this->_fields = $fields;
    }

    /**
     * Return repository name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Return database connection
     * @return \SAPF\Database\SQLDatabase
     */
    public function getDb()
    {
        return $this->_repositoryManager->getDb();
    }

    /**
     * Register extension
     * @param \SAPF\Repository\Extension\ExtensionInterface $ext
     */
    public function registerExtension(\SAPF\Repository\Extension\ExtensionInterface $ext)
    {
        $ext->setRepository($this);
        $this->_extensions[strtolower($ext->getName())] = $ext;
    }

    /**
     * Return repository extensions
     * @return array
     */
    public function getExtensions()
    {
        return $this->_extensions;
    }

    /**
     * Return repository extension
     * @return \SAPF\Repository\Extension\ExtensionInterface
     */
    public function getExtension($name)
    {
        return $this->_extensions[strtolower($name)];
    }

    /**
     * Return fields
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Return Model primary key name
     * @return string 
     */
    public function getPrimaryKeyName()
    {
        return $this->_model()->getPrimaryKeyName();
    }

    /**
     * Set repositoryManager
     * @param \SAPF\Repository\RepositoryManager $repositoryManager
     * @return \SAPF\Repository\RepositoryAbstract
     */
    public function setRepositoryManager(\SAPF\Repository\RepositoryManager $repositoryManager)
    {
        $this->_repositoryManager = $repositoryManager;
        return $this;
    }

    /**
     * Return RepositoryManager
     * @return RepositoryManager
     */
    public function getRepositoryManager()
    {
        return $this->_repositoryManager;
    }

    /**
     * Return model
     * @return \SAPF\Database\Model
     */
    public function getModel()
    {
        return $this->_model();
    }

    /**
     * Fetch one entity by params
     * @param array $param Fetch params
     * @return array Fetched entity or null
     */
    public function fetch(array $param)
    {
        $request = $param;
        $this->_fetchPre($param, $request);

        $tuple = $this->fetcher($param)->fetch();

        if (!$tuple) {
            return null;
        }

        $this->tuple2entity($param, $request, $tuple);

        foreach ($this->_extensions as $s) {
            $s->tuple2entity($param, $request, $tuple);
        }

        $this->_fetchPost($param, $request, $tuple);

        return $tuple;
    }

    /**
     * Fetch entities by params
     * @param array $param Fetch params
     * @return array Array of entities (if none - return empty array)
     */
    public function fetchAll(array $param)
    {
        $request = $param;
        $this->_fetchAllPre($param, $request);

        $tuples = $this->fetcher($param)->fetchAll();

        if (!$tuples) {
            return [];
        }

        $this->tuples2entities($param, $request, $tuples);

        foreach ($this->_extensions as $s) {
            $s->tuples2entities($param, $request, $tuples);
        }

        $this->_fetchAllPost($param, $request, $tuples);

        return $tuples;
    }

    /**
     * Checks if entity with given params exists
     * @param array $param
     * @return boolean
     */
    public function has(array $param)
    {
        return $this->fetcher($param)->has();
    }

    /**
     * Get count of entities by given params
     * @param array $param
     * @return int
     */
    public function count(array $param)
    {
        return $this->fetcher($param)->count();
    }

    /**
     * Remove entity
     * Alias for remove()
     * @param array $entity
     * @return boolean
     */
    public function delete(array $entity)
    {
        return $this->remove($entity); // alias
    }

    /**
     * Remove entity
     * @param array $entity
     * @return boolean
     */
    public function remove(array $entity)
    {
        $model = $this->_model();

        if ($entity['operation']) {
            if ($entity[$model->getPrimaryKeyName()]) {
                $entityPre = $this->fetch([
                    $model->getPrimaryKeyName() => $entity[$model->getPrimaryKeyName()]
                ]);
            }
            $this->_removePre($entity, $entityPre);
        }

        $ret = $this->_removeEntity($model, $entity);

        foreach ($this->_extensions as $s) {
            $s->remove($entity);
        }

        if ($entity['operation']) {
            $this->_removePost($entity, $entityPre);
        }

        return $ret;
    }

    /**
     * Save entity
     * @param array $entity
     */
    public function save(array $entity)
    {
        $model = $this->_model();
        $model->fields($this->_fields);

        if ($entity['operation']) {
            if ($entity[$model->getPrimaryKeyName()]) {
                $entityPre = $this->fetch([
                    $model->getPrimaryKeyName() => $entity[$model->getPrimaryKeyName()]
                ]);
            }
            $this->_savePre($entity, $entityPre);
        }

        $this->_saveEntity($model, $entity);

        foreach ($this->_extensions as $s) {
            $s->save($entity);
        }

        if ($entity['operation']) {
            if ($entity[$model->getPrimaryKeyName()]) {
                $entityPost = $this->fetch([
                    $model->getPrimaryKeyName() => $entity[$model->getPrimaryKeyName()]
                ]);
            }
            $this->_savePost($entity, $entityPre, $entityPost);
        }

        return $entity;
    }

    /**
     * Fetcher used for fetch, fetchAll, count, has
     * @param array $param
     * @param \SAPF\Database\Model $model
     * @return \SAPF\Database\Model
     */
    public function fetcher(array $param = array())
    {
        $model = $this->_model();
        $model->fields($this->_fields);

        $this->_fetcher($param, $model);

        foreach ($this->_extensions as $s) {
            $s->fetcher($this, $param, $model);
        }

        if (is_array($param['field'])) {
            $model->fields($param['field']);
            unset($param['field']);
        }

        // make sure we will fetch entity primary key
        if (!in_array($this->getPrimaryKeyName(), $model->getFields())) {
            $c   = $model->getFields();
            $c[] = $this->getPrimaryKeyName();
            $model->fields($c);
        }

        $model->params($param);
        return $model;
    }

    /**
     * Transform record from database to entity
     * @param array $tuple
     */
    public function tuple2entity(array &$param, array $request, array &$tuple)
    {
        $this->_tuple2entity($param, $request, $tuple);
    }

    /**
     * Transform records from database to entities
     * @param array $tuples
     */
    public function tuples2entities(array &$param, array $request, array &$tuples)
    {
        $this->_tuples2entities($param, $request, $tuples);
    }

    // protected functions

    protected function _saveEntity(\SAPF\Database\Model $model, array &$entity)
    {
        $filtered = $model->filterData($entity);

        $ret = $model->save($filtered);
        if ($ret > 0) {
            $entity[$model->getPrimaryKeyName()] = $ret;
            $entity['isInserted']                = TRUE;
        }
    }

    protected function _removeEntity(\SAPF\Database\Model $model, array &$entity)
    {
        if ($entity[$model->getPrimaryKeyName()]) {
            return $model->param($model->getPrimaryKeyName(), $entity[$model->getPrimaryKeyName()])->remove();
        }

        throw new RepositoryException("\$entity must contain \"" . $model->getPrimaryKeyName() . "\"!");
    }

    protected function _model()
    {
        return new \SAPF\Database\Model($this->getDb(), $this->getName(), $this->getName() . "_id");
    }

    protected function _tuple2entity(array &$param, array $request, array &$tuple)
    {
        
    }

    protected function _tuples2entities(array &$param, array $request, array &$tuples)
    {
        
    }

    protected function _fetcher(array &$param, \SAPF\Database\Model &$model)
    {
        
    }

    protected function _fetchPre(array &$param, array $request)
    {
        
    }

    protected function _fetchPost(array &$param, array $request, array &$tuple)
    {
        
    }

    protected function _fetchAllPre(array &$param, array $request)
    {
        
    }

    protected function _fetchAllPost(array &$param, array $request, array &$tuples)
    {
        
    }

    protected function _savePre(array &$entity, array $entityPre)
    {
        
    }

    protected function _savePost(array &$entity, array $entityPre, array $entityPost)
    {
        
    }

    protected function _removePre(array &$entity, array $entityPre)
    {
        
    }

    protected function _removePost(array &$entity, array $entityPre)
    {
        
    }

}
