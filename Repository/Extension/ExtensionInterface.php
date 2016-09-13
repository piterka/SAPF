<?php

namespace SAPF\Repository\Extension;

interface ExtensionInterface
{

    public abstract function setRepository(\SAPF\Repository\RepositoryAbstract $repository);

    public abstract function fetcher(array &$param, \SAPF\Database\Model &$model);

    public abstract function remove(array $entity);

    public abstract function save(array $entity);

    public abstract function tuple2entity(array $param, array $request, array &$tuple);

    public abstract function tuples2entities(array $param, array $request, array &$tuples);
}
