<?php

namespace SAPF\Translate;

interface TranslateAwareInterface
{

    /**
     * Sets translate
     * @param \SAPF\Translate\TranslateInterface $translate
     */
    public function setTranslate(TranslateInterface $translate);
}
