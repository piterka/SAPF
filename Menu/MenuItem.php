<?php

namespace SAPF\Menu;

class MenuItem extends MenuObject
{

    protected $_content;
    protected $_decor;
    protected $_url;
    protected $_forceActive = null;

    public function __construct()
    {
        $this->_decor = new \SAPF\Decor\DecorHtmlTag();
        $this->_decor->setTag("li");
    }

    /**
     * Gets force active flag
     * @return boolean|null
     */
    public function getForceActive()
    {
        return $this->_forceActive;
    }

    /**
     * Sets force active flag
     * @param boolean|null $forceActive
     * @return \SAPF\Menu\MenuItem
     */
    public function setForceActive($forceActive)
    {
        $this->_forceActive = $forceActive;
        return $this;
    }

    /**
     * Get item URL
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Set item URL
     * @param type $url
     * @return \SAPF\Menu\MenuItem
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * Get menu item decor
     * @return \SAPF\Decor\DecorInterface
     */
    public function getDecor()
    {
        return $this->_decor;
    }

    /**
     * Set menu item decor
     * @param \SAPF\Decor\DecorInterface $decor
     * @return \SAPF\Menu\MenuItem
     */
    public function setDecor(\SAPF\Decor\DecorInterface $decor)
    {
        $this->_decor = $decor;
        return $this;
    }

    /**
     * Item display name
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Sets item displayname
     * @param string $content
     * @return \SAPF\Menu\MenuObject
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Return true if item should be marked as active
     * @return boolean active
     */
    public function isActive()
    {
        if ($this->_forceActive !== NULL) {
            return (boolean) $this->_forceActive;
        }

        $urlR = \SAPF\URL\URL::fromRequest($this->getMenu()->getRequest());
        $urlI = new \SAPF\URL\URL($this->_url);

        if (strpos($urlR->getPath(), $urlI->getPath()) === 0) {
            foreach ($urlI->getQueryParams() as $k => $v) {
                if ($urlR->getQueryParam($k) != $v) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Rendered item
     * @return string
     */
    public function render()
    {
        return $this->_decor->decorate($this->getContent());
    }

}
