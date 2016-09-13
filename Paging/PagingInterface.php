<?php

namespace SAPF\Paging;

interface PagingInterface
{

    public function setMax($max);

    public function getMax();

    public function setPerPage($perPage);

    public function getPerPage();

    public function setPage($page);

    public function getPage();

    public function getDBLimit();

    public function getOffset();

    public function getPagesToView($includeDotNulls, $left, $center, $right);
}
