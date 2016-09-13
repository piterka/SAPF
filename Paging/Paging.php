<?php

namespace SAPF\Paging;

/*
 * Example of use:
 * 
 *
 *       $paging = new \SAPF\Paging\Paging();
 *       $paging->setPerPage(15);
 *       $paging->setMax(1000);
 *
 *       $pages = $paging->getPagesToView();
 *       foreach ($pages as $k) {
 *           if ($k == null) {
 *               echo "... ";
 *           } else {
 *               echo $k . " ";
 *           }
 *       }
 *
 * 
 */

class Paging implements \SAPF\Paging\PagingInterface
{

    protected $_perPage;
    protected $_page;
    protected $_maxPage;
    protected $_max;

    public function __construct($page)
    {
        $this->_page = $page > 0 ? $page : 1;
    }

    public function setPerPage($perPage)
    {
        $this->_perPage = $perPage;
        return $this;
    }

    public function getPerPage()
    {
        return $this->_perPage;
    }

    public function setMax($max)
    {
        $this->_max     = $max;
        $this->_maxPage = $max / $this->_perPage;
        if ($max % $this->_perPage > 0) {
            $this->_maxPage += 1;
        }
        return $this;
    }

    public function getMax()
    {
        return $this->_max;
    }

    public function setPage($page)
    {
        $this->_page = $page;
        return $this;
    }

    public function getPage()
    {
        return $this->_page;
    }

    public function getDBLimit()
    {
        return array($this->getOffset(), $this->_perPage);
    }

    public function getOffset()
    {
        return ($this->_page - 1) * $this->_perPage;
    }

    public function getPagesToView($includeDotNulls = TRUE, $left = 3, $center = 2, $right = 3)
    {
        $pages = array();

        $a = FALSE;
        for ($i = 1; $i <= $this->_maxPage; $i++) {
            if ($i <= $left) {
                $pages[] = $i;
                $a       = FALSE;
            }
            elseif (abs($this->_maxPage - $i) < $right) {
                $pages[] = $i;
                $a       = FALSE;
            }
            else if (abs($this->_page - $i) < $center + 1) {
                $pages[] = $i;
                $a       = FALSE;
            }
            else if (!$a && $includeDotNulls) {
                $a       = TRUE;
                $pages[] = null;
            }
        }

        return $pages;
    }

}
