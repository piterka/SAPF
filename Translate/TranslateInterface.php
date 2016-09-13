<?php

namespace SAPF\Translate;

interface TranslateInterface
{

    /**
     * Translates string and replaces replacements
     * @param string $string
     * @param array $replacements
     */
    public function translate($string, array $replacements = []);
}
