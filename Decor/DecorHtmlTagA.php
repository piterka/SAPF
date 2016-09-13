<?php

namespace SAPF\Decor;

class DecorHtmlTagA extends \SAPF\Decor\DecorHtmlTag
{

    protected $_tag = 'a';

    const TARGET_BLANK  = '_blank';
    const TARGET_SELF   = '_self';
    const TARGET_PARENT = '_parent';
    const TARGET_TOP    = '_top';

    public function setHref($url)
    {
        $this->setAttr("href", $url);
        return $this;
    }

    public function getHref()
    {
        return $this->getAttr("href");
    }

    public function setTarget($target)
    {
        $this->setAttr("target", $target);
        return $this;
    }

    public function getTarget()
    {
        return $this->getAttr("target");
    }

}
