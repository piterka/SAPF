<?php

namespace SAPF\Repository;

class RepositoryMaster extends \SAPF\Repository\RepositorySlave
{

    public function __construct($name, $fields, $master = false)
    {
        parent::__construct($name, $fields, $master);

        if (!in_array($this->getConnectorField(), $fields)) {
            $fields[] = $this->getConnectorField();
        }
        $this->_fields = $fields;
    }

    public function getConnectorField()
    {
        return $this->getName() . '_type';
    }

    public function save(array $entity)
    {
        if (!isset($entity[$this->getConnectorField()])) {
            throw new \SAPF\Repository\RepositoryException("Expected \"" . $this->getConnectorField() . "\" in \$entity");
        }

        if (!isset($entity[$this->getName() . '_saveEntityRaw'])) {
            return $this->_repositoryManager->{$entity[$this->getConnectorField()]}->save($entity);
        }

        return parent::save($entity);
    }

    public function remove(array $entity)
    {
        if (!isset($entity[$this->getConnectorField()])) {
            throw new \SAPF\Repository\RepositoryException("Expected \"" . $this->getConnectorField() . "\" in \$entity");
        }

        if (!isset($entity[$this->getName() . '_removeEntityRaw'])) {
            return $this->_repositoryManager->{$entity[$this->getConnectorField()]}->remove($entity);
        }

        return parent::remove($entity);
    }

}
