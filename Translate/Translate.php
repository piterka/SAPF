<?php

namespace SAPF\View;

class Translate implements \SAPF\Translate\TranslateInterface
{

    protected $_selectedLang = 'pl';
    protected $_langFolder;
    protected $_cache;

    public function __construct($selectedLang = 'pl', $langFolder = "lang")
    {
        $this->_selectedLang = $selectedLang;
        $this->_langFolder   = $langFolder;
        $this->_cache        = [];
    }

    /**
     * Translates string and replaces replacements
     * @param string $string
     * @param array $replacements
     */
    public function translate($text, $params = [])
    {
        $text = $this->_getTranslate($text, $this->_selectedLang);

        // apply params
        if (is_array($params)) {
            foreach ($params as $param => $value) {
                $text = str_replace($param, $value, $text);
            }
        }

        return $text;
    }

    /**
     * Gets selected translate lang
     * @return string
     */
    public function getSelectedLang()
    {
        return $this->_selectedLang;
    }

    /**
     * Gets translate data folder
     * @return string
     */
    public function getLangFolder()
    {
        return $this->_langFolder;
    }

    /**
     * Sets selected translate lang
     * @return Translate
     */
    public function setSelectedLang($selectedLang)
    {
        $this->_selectedLang = $selectedLang;
        return $this;
    }

    /**
     * Sets selected translate data folder
     * @return Translate
     */
    public function setLangFolder($langFolder)
    {
        $this->_langFolder = $langFolder;
        return $this;
    }

    protected function _getTranslate($string, $lang = 'pl')
    {
        $lang = strtolower($lang);

        // load translate
        $translateFile = $this->_langFolder . '/' . $lang . '.json';
        if (!array_key_exists($lang, $this->_cache)) {
            if (file_exists($translateFile)) {
                $data                = file_get_contents($translateFile);
                $this->_cache[$lang] = json_decode($data, true);
            }
        }

        if (!is_array($this->_cache[$lang])) {
            $this->_cache[$lang] = array();
        }

        // return translate
        if ($this->_cache[$lang][$string]) {
            return $this->_cache[$lang][$string];
        }

        // add string to translate
        $this->_cache[$lang][$string] = "##LANG#" . $string;
        file_put_contents($translateFile, json_encode($this->_cache[$lang], JSON_PRETTY_PRINT));
        return "##LANG#" . $string;
    }

}
