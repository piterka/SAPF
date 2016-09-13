<?php

namespace SAPF\Translate;

trait TranslateAwareTrait
{

    protected $_translate;

    /**
     * Sets translate
     * @param \SAPF\Translate\TranslateInterface $translate
     */
    public function setTranslate(TranslateInterface $translate)
    {
        $this->_translate = $translate;
        return $this;
    }

    /**
     * Gets translate
     * @return \SAPF\Translate\TranslateInterface
     */
    public function getTranslate()
    {
        return $this->_translate;
    }

}
