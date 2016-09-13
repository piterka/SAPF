<?php

namespace SAPF\Menu;

class Menu extends MenuObject
{

    protected $_request;
    protected $_decor;
    protected $_objects = [];

    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->_request = $request;
        $this->_decor   = new \SAPF\Decor\DecorHtmlTag();
        $this->_decor->setTag("ul");
    }

    /**
     * Menu decor
     * (Decor subject is concatenation of all rendered objects)
     * @return \SAPF\Decor\DecorInterface
     */
    public function getDecor()
    {
        return $this->_decor;
    }

    /**
     * Set menu decor
     * (Decor subject is concatenation of all rendered objects)
     * @param \SAPF\Decor\DecorInterface $decor
     * @return \SAPF\Menu\Menu
     */
    public function setDecor(\SAPF\Decor\DecorInterface $decor)
    {
        $this->_decor = $decor;
        return $this;
    }

    /**
     * Return menu context request
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Add object to menu
     * @param \SAPF\Menu\MenuObject $item
     * @return \SAPF\Menu\Menu
     */
    public function addObject(MenuObject $item)
    {
        $item->setMenu($this);
        $this->_objects[] = $item;
        return $this;
    }

    /**
     * Return all menu objects
     * @return array
     */
    public function getObjects()
    {
        return $this->_objects;
    }

    /**
     * Set menu objects
     * @param array $items
     * @return \SAPF\Menu\Menu
     */
    public function setObjects($items)
    {
        $this->_objects = [];
        foreach ($items as $item) {
            $this->addObject($item);
        }
        return $this;
    }

    public function isActive()
    {
        foreach ($this->_objects as $o) {
            if ($o->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Rendered menu
     * @return string
     */
    public function render()
    {
        $items = "";
        foreach ($this->_objects as $obj) {
            $items .= $obj->render(false);
        }
        return $this->_decor->decorate($items);
    }

    public function __toString()
    {
        return $this->render();
    }

}
