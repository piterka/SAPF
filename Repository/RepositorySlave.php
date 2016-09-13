<?php

namespace SAPF\Repository;

class RepositorySlave extends \SAPF\Repository\RepositoryAbstract
{

    protected $_masterName = false;

    public function __construct($name, $fields, $master = false)
    {
        $this->_masterName = $master;
        parent::__construct($name, $fields);
    }

    public function getMasterRepo()
    {
        return $this->_repositoryManager->{$this->_masterName};
    }

    protected function _fetcher(array &$param, \SAPF\Database\Model &$model)
    {
        $masterRepo = $this->getMasterRepo();
        while ($masterRepo != null) {
            if (!($masterRepo instanceof \SAPF\Repository\RepositoryMaster)) {
                throw new \SAPF\Repository\RepositoryException("\$master repository must implement \SAPF\Repository\RepositoryMaster");
            }

            // join to master table
            $model->joinLeft($masterRepo->getModel()->getTableName(), [
                '!' . $model->getPrimaryKeyName() => '`' . $masterRepo->getPrimaryKeyName() . '`'
            ]);

            // add fields to select from master table
            $fields = $model->getFields();
            if ($fields != "*") {
                foreach ($masterRepo->getFields() as $f) {
                    $fields[] = $f;
                }
                $model->fields($fields);
            }

            $masterRepo = $masterRepo->getMasterRepo();
        }

        parent::_fetcher($param, $model);
    }

    protected function _removeEntity(\SAPF\Database\Model $model, array &$entity)
    {
        if ($this->_masterName) {
            if (!($this->_repositoryManager->{$this->_masterName} instanceof \SAPF\Repository\RepositoryMaster)) {
                throw new \SAPF\Repository\RepositoryException("\$master repository must implement RepositoryMaster");
            }

            $prim = $this->_repositoryManager->{$this->_masterName}->getPrimaryKeyName();
            if (isset($entity[$model->getPrimaryKeyName()])) {
                $entity[$prim] = $entity[$model->getPrimaryKeyName()];
            }

            $operation                                       = $entity['operation'];
            $entity['operation']                             = false;
            $entity[$this->_masterName . '_removeEntityRaw'] = true;
            $this->_repositoryManager->{$this->_masterName}->remove($entity); // remove main entity
            $entity['operation']                             = $operation;
            unset($entity[$this->_masterName . '_removeEntityRaw']);

            if (isset($entity[$prim])) {
                $entity[$model->getPrimaryKeyName()] = $entity[$prim];
            }
        }

        return parent::_removeEntity($model, $entity); // remove entity
    }

    protected function _saveEntity(\SAPF\Database\Model $model, array &$entity)
    {
        if ($this->_masterName) {
            if (!($this->_repositoryManager->{$this->_masterName} instanceof RepositoryMaster)) {
                throw new \SAPF\Repository\RepositoryException("\$master repository must implement RepositoryMaster");
            }

            $prim = $this->_repositoryManager->{$this->_masterName}->getPrimaryKeyName();
            if (isset($entity[$model->getPrimaryKeyName()])) {
                $entity[$prim] = $entity[$model->getPrimaryKeyName()];
            }

            $entity[$this->getMasterRepo()->getConnectorField()] = $this->getName();

            $operation                                     = $entity['operation'];
            $entity['operation']                           = false;
            $entity[$this->_masterName . '_saveEntityRaw'] = true;
            $entityMainSaved                               = $this->_repositoryManager->{$this->_masterName}->save($entity); // save main entity
            if ($operation) {
                $entity['operation'] = $operation;
            }
            unset($entity[$this->_masterName . '_saveEntityRaw']);

            if ($entityMainSaved['isInserted']) {
                $entity['isInserted'] = $entityMainSaved['isInserted'];
                $this->_savePreInsert($model, $entity);
            }
            if (isset($entityMainSaved[$prim])) {
                $entity[$model->getPrimaryKeyName()] = $entityMainSaved[$prim];
            }
        }

        parent::_saveEntity($model, $entity); // save entity
    }

    protected function _savePreInsert(\SAPF\Database\Model $model, array &$entity)
    {
        
    }

}
