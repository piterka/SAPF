<?php

namespace SAPF\Filter;

class FilterStripHtml implements \SAPF\Filter\FilterInterface
{

    protected $_allowedTags       = array('a', 'b', 'span', 'div', 'p', 'u', 'i', 'strong', 'br', 'big', 'center', 'code', 'pre', 'hr', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'i', 's', 'q', 'sup', 'sub', 'img');
    protected $_allowedAttributes = array(
        'href'   => array(
            "/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/",
            "/^[a-zA-Z0-9\/._]*$/",
            "",
        ),
        'src'    => array(
            "/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/",
            "/^[a-zA-Z0-9\/._]*$/",
            "",
        ),
        'title', 'alt', 'value', 'disabled',
        'id'     => "/^[a-zA-Z0-9._]*$/",
        'lang'   => "/^[a-zA-Z0-9._]*$/",
        'width'  => "/^[0-9]*$/",
        'height' => "/^[0-9]*$/",
        'style', 'class'
    );

    public function getAllowedTags()
    {
        return $this->_allowedTags;
    }

    public function setAllowedTags($allowedTags)
    {
        $this->_allowedTags = $allowedTags;
        return $this;
    }

    public function getAllowedAttributes()
    {
        return $this->_allowedAttributes;
    }

    public function setAllowedAttributes($allowedAttributes)
    {
        $this->_allowedAttributes = $allowedAttributes;
        return $this;
    }

    public function filter($input)
    {
        return $this->_stripHtml($input, $this->_allowedTags, $this->_allowedAttributes);
    }

    protected function _stripHtml($data, $allowedTags, $allowedAttributes)
    {
        $tags = "";
        foreach ($allowedTags as $tag) {
            $tags .= "<" . $tag . "><" . $tag . "/>";
        }
        $stripped = strip_tags($data, $tags);

        $dom   = new \DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML($stripped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);            // create a new XPath
        $nodes = $xpath->query('//*');  // Find elements with a style attribute
        foreach ($nodes as $node) {              // Iterate over found elements
            $attributes = $node->attributes;
            $toRemove   = array();
            foreach ($attributes as $attrName => $attrNode) {
                $attrName = strtolower($attrName);
                if (!array_key_exists($attrName, $allowedAttributes) && !in_array($attrName, $allowedAttributes)) {
                    $toRemove[] = $attrName;
                    continue;
                }

                if (array_key_exists($attrName, $allowedAttributes)) {
                    $regex = $allowedAttributes[$attrName];
                    if (!is_array($regex)) {
                        $regex = array($regex);
                    }

                    $valid = FALSE;
                    foreach ($regex as $r) {
                        if ($r != "*") {
                            if ($r == "") {
                                $attrNode->nodeValue = "";
                            }
                            else {
                                if (preg_match($r, $attrNode->nodeValue)) {
                                    $valid = true;
                                    break;
                                }
                            }
                        }
                    }
                    if (!$valid) {
                        $toRemove[] = $attrName;
                    }
                    else {
                        $attrNode->nodeValue = h($attrNode->nodeValue);
                    }
                }
            }
            foreach ($toRemove as $r) {
                $node->removeAttribute($r);
            }
        }

        $innerHTML = $dom->saveXML($dom);

        return $innerHTML;
    }

}
