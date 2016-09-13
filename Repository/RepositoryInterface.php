<?php

namespace SAPF\Repository;

interface RepositoryInterface
{

    public function fetcher(array $param = []);

    public function fetch(array $param);

    public function fetchAll(array $param);

    public function has(array $param);

    public function count(array $param);

    public function save(array $entity);

    public function remove(array $entity);

    public function delete(array $entity);

    public function registerExtension(Extension\ExtensionAbstract $ext);

    public function setRepositoryManager(\SAPF\Repository\RepositoryManager $repositoryManager);
}
