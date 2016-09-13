<?php

namespace SAPF\Menu;

abstract class MenuObject
{

    use \SAPF\DataBucket\DataBucketTrait;

    protected $_menu;

    /**
     * Sets item menu
     * @param Menu $menu
     * @return \SAPF\Menu\MenuObject
     */
    public function setMenu(Menu $menu)
    {
        $this->_menu = $menu;
        return $this;
    }

    /**
     * Return menu that object is attached to
     * @return Menu
     */
    public function getMenu()
    {
        return $this->_menu;
    }

    public abstract function isActive();

    public abstract function render();
}
