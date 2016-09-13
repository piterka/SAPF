<?php

namespace SAPF\View;

class Head
{

    public $template = [
        'charset'      => [
            'mode'     => 'item',
            'template' => "\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset={charset}\">\n",
            'var'      => 'charset',
        ],
        'title'        => [
            'mode'     => 'item',
            'template' => "\t<title>{content}</title>\n",
            'var'      => 'content',
        ],
        'keywords'     => [
            'mode'      => 'list',
            'template'  => "{words}",
            'var'       => 'words',
            'prepend'   => "\t<meta name=\"keywords\" content=\"",
            'separator' => ', ',
            'append'    => "\" />\n",
        ],
        'robots'       => [
            'mode'      => 'list',
            'template'  => "{param}",
            'var'       => 'param',
            'prepend'   => "\t<meta name=\"robots\" content=\"",
            'separator' => ', ',
            'append'    => "\" />\n",
        ],
        'description'  => [
            'mode'      => 'list',
            'template'  => "{description}",
            'var'       => 'description',
            'prepend'   => "\t<meta name=\"description\" content=\"",
            'separator' => '. ',
            'append'    => "\" />\n",
        ],
        'favicon'      => [
            'mode'     => 'item',
            'template' => "\t<link href=\"{href}\" rel=\"shortcut icon\">\n",
            'var'      => 'href',
        ],
        'canonical'    => [
            'mode'     => 'item',
            'template' => "\t<link rel=\"canonical\" href=\"{content}\" />\n",
            'var'      => 'content',
        ],
        'next'         => [
            'mode'     => 'item',
            'template' => "\t<link rel=\"next\" href=\"{content}\" />",
            'var'      => 'content',
        ],
        'prev'         => [
            'mode'     => 'item',
            'template' => "\t<link rel=\"prev\" href=\"{content}\" />",
            'var'      => 'content',
        ],
        'publisher'    => [
            'mode'     => 'item',
            'template' => "\t<link rel=\"publisher\" href=\"{content}\" />",
            'var'      => 'content'
        ],
        'rss'          => [
            'mode'       => 'list',
            'template'   => "\t<link href=\"{href}\" title=\"{title}\" type=\"application/rss+xml\" rel=\"alternate\" {attr}/>\n",
            'var'        => 'href',
            'val'        => ['attr' => ''],
            'dontEscape' => ['attr'],
        ],
        'atom'         => [
            'mode'       => 'list',
            'template'   => "\n<link href=\"{href}\" title=\"{title}\" type=\"application/atom+xml\" rel=\"alternate\" {attr}/>",
            'var'        => 'href',
            'val'        => ['attr' => ''],
            'dontEscape' => ['attr'],
        ],
        'css'          => [
            'mode'       => 'list',
            'template'   => "\t<link rel=\"stylesheet\" type=\"text/css\" href=\"{href}\" {attr}/>\n",
            'var'        => 'href',
            'val'        => ['attr' => ''],
            'dontEscape' => ['attr'],
        ],
        'cssIE7'       => [
            'mode'       => 'list',
            'template'   => "\t<!--[if IE 7]><link rel=\"stylesheet\" type=\"text/css\" href=\"{href}\" {attr}/><![endif]-->\n",
            'var'        => 'href',
            'val'        => ['attr' => ''],
            'dontEscape' => ['attr'],
        ],
        'js'           => [
            'mode'       => 'list',
            'template'   => "\t<script src=\"{src}\" type=\"text/javascript\"{attr}>{content}</script>\n",
            'var'        => 'src',
            'val'        => ['attr' => '', 'content' => ''],
            'dontEscape' => ['attr'],
        ],
        'viewport'     => [
            'mode'     => 'list',
            'template' => "\t<meta name=\"viewport\" content=\"{content}\"/>\n",
            'var'      => 'content',
        ],
        'jscode'       => [
            'mode'       => 'list',
            'template'   => "{content}",
            'var'        => 'content',
            'prepend'    => "\t<script type=\"text/javascript\">\n",
            'separator'  => "\n",
            'append'     => "\n</script>\n",
            'dontEscape' => ['content'],
        ],
        'jsblock'      => [
            'mode'       => 'list',
            'template'   => "\t<script type=\"text/javascript\"{attr}>\n{content}\n\t</script>\n",
            'var'        => 'content',
            'val'        => ['attr' => ''],
            'dontEscape' => ['content', 'attr'],
        ],
        'csscode'      => [
            'mode'       => 'list',
            'template'   => "{content}",
            'var'        => 'content',
            'prepend'    => "\t<style type=\"text/css\">\n",
            'separator'  => "\n",
            'append'     => "\n\t</style>\n",
            'dontEscape' => ['content'],
        ],
        'cssblock'     => [
            'mode'       => 'list',
            'template'   => "\t<style type=\"text/css\"{attr}>\n{content}\n\t</style>\n",
            'var'        => 'content',
            'val'        => ['attr' => ''],
            'dontEscape' => ['content', 'attr'],
        ],
        'meta'         => [
            'mode'       => 'list',
            'template'   => "\t<meta name=\"{name}\" content=\"{content}\"{attr}>\n",
            'var'        => 'content',
            'val'        => ['name' => '', 'content' => '', 'attr' => ''],
            'dontEscape' => ['attr'],
        ],
        'metaProperty' => [
            'mode'       => 'list',
            'template'   => "\t<meta property=\"{property}\" content=\"{content}\"{attr}>\n",
            'var'        => 'content',
            'val'        => ['property' => '', 'content' => '', 'attr' => ''],
            'dontEscape' => ['attr'],
        ],
        'script'       => [
            'mode'       => 'list',
            'template'   => "\t<script{attr}>\n{content}\n\t</script>\n",
            'var'        => 'content',
            'val'        => ['attr' => '', 'content' => ''],
            'dontEscape' => ['content', 'attr'],
        ],
        'raw'          => array(
            'mode'       => 'list',
            'template'   => "\n\t{content}\n",
            'var'        => 'content',
            'dontEscape' => ['content'],
        ),
    ];
    // 
    protected $_data = [];

    public function head($type, $data = null)
    {
        if (!array_key_exists($type, $this->template)) {
            throw new \InvalidArgumentException("Template: $type dont exist!");
        }

        if (is_string($data)) {
            $data = [$this->template[$type]['var'] => $data];
        }

        if ($this->template[$type]['mode'] == 'item') {
            $this->_data[$type] = $data;
        }
        else {
            $this->_data[$type][] = $data;
        }

        return $this;
    }

    public function render($excludeList = [])
    {
        $output = '';

        foreach ($this->template as $type => $tpl) {
            if (in_array($type, $excludeList)) {
                continue;
            }
            $output .= $this->renderType($type);
        }

        return $output;
    }

    public function renderType($type)
    {
        $data = $this->_data[$type];
        $tpl  = $this->template[$type];

        if (!isset($data)) {
            return '';
        }

        if ($tpl['mode'] == 'item') {

            $data += (array) $tpl['val'];
            $output = $tpl['template'];
            foreach ($data as $var => $val) {
                if (!$tpl['dontEscape'] || !in_array($var, $tpl['dontEscape'])) {
                    $val = htmlspecialchars($val);
                }
                $output = str_replace("{{$var}}", $val, $output);
            }

            return $output;
        }
        else if ($tpl['mode'] == 'list') {
            $outputArray = [];

            if ($tpl['reverse']) {
                $data = array_reverse($data);
            }

            foreach ($data as $i) {
                $i += (array) $tpl['val'];
                $output = $tpl['template'];
                foreach ($i as $var => $val) {
                    if (!$tpl['dontEscape'] || !in_array($var, $tpl['dontEscape'])) {
                        $val = htmlspecialchars($val);
                    }
                    $output = str_replace("{{$var}}", $val, $output);
                }
                $outputArray[] = $output;
            }

            return
                    (string) $tpl['prepend']
                    . implode((string) $tpl['separator'], $outputArray)
                    . (string) $tpl['append'];
        }
        else {
            return implode('', $data);
        }
    }

    public function clear($type = null)
    {
        if ($type) {
            unset($this->_data[$type]);
        }
        else {
            $this->_data = [];
        }
        return $this;
    }

    public function charset($charset)
    {
        return $this->head('charset', ($charset));
    }

    public function title($title)
    {
        return $this->head('title', ($title));
    }

    public function keywords($keywords)
    {
        return $this->head('keywords', ($keywords));
    }

    public function description($description)
    {
        return $this->head('description', ($description));
    }

    public function robots($robots)
    {
        return $this->head('robots', ($robots));
    }

    public function favicon($favicon)
    {
        return $this->head('favicon', ($favicon));
    }

    public function canonical($content)
    {
        return $this->head('canonical', ($content));
    }

    public function rss($uri, $title = null, $attr = "")
    {
        return $this->head('rss', ['href' => ($uri), 'title' => ($title), 'attr' => $attr]);
    }

    public function atom($uri, $title = null, $attr = "")
    {
        return $this->head('atom', ['href' => ($uri), 'title' => ($title), 'attr' => $attr]);
    }

    public function css($uri, $attr = "")
    {
        return $this->head('css', ['href' => ($uri), 'attr' => $attr]);
    }

    public function cssIE7($uri, $attr = "")
    {
        return $this->head('cssIE7', ['href' => ($uri), 'attr' => $attr]);
    }

    public function csscode($content)
    {
        return $this->head('csscode', $content);
    }

    public function cssblock($content, $attr = "")
    {
        return $this->head('cssblock', ['content' => $content, 'attr' => $attr]);
    }

    public function viewport($content)
    {
        return $this->head('viewport', ['content' => ($content)]);
    }

    public function js($uri, $content = null, $attr = "")
    {
        return $this->head('js', ['attr' => $attr, 'src' => ($uri), 'content' => $content]);
    }

    public function jscode($content)
    {
        return $this->head('jscode', $content);
    }

    public function jsblock($content, $attr = "")
    {
        return $this->head('jsblock', ['content' => $content, 'attr' => $attr]);
    }

    public function publisher($content)
    {
        return $this->head('publisher', $content);
    }

    public function pageMap($data)
    {
        return $this->head('raw', "<!--\n\t<PageMap>\n" . $data . "\n\t</PageMap>-->");
    }

    public function raw($data)
    {
        return $this->head('raw', $data);
    }

    public function meta($name, $content, $attr = "")
    {
        return $this->head('meta', [
                    'name'    => ($name),
                    'content' => $content,
                    'attr'    => $attr,
        ]);
    }

    public function metaProperty($property, $content, $attr = "")
    {
        return $this->head('metaProperty', [
                    'property' => ($property),
                    'content'  => $content,
                    'attr'     => $attr,
        ]);
    }

    public function ogTitle($content)
    {
        return $this->head('metaProperty', [
                    'property' => 'og:title',
                    'content'  => ($content),
        ]);
    }

    public function ogType($content)
    {
        return $this->head('metaProperty', [
                    'property' => 'og:type',
                    'content'  => ($content),
        ]);
    }

    public function ogUrl($content)
    {
        return $this->head('metaProperty', [
                    'property' => 'og:url',
                    'content'  => ($content),
        ]);
    }

    public function ogImage($content)
    {
        return $this->head('metaProperty', [
                    'property' => 'og:image',
                    'content'  => ($content),
        ]);
    }

    public function ogSiteName($content)
    {
        return $this->head('metaProperty', [
                    'property' => 'og:site_name',
                    'content'  => ($content),
        ]);
    }

    public function ogDescription($content)
    {
        return $this->head('metaProperty', [
                    'property' => 'og:description',
                    'content'  => ($content),
        ]);
    }

    public function next($content)
    {
        return $this->head('next', ($content));
    }

    public function prev($content)
    {
        return $this->head('prev', ($content));
    }

    public function fbAppId($content)
    {
        return $this->head('metaProperty', [
                    'property' => 'fb:app_id',
                    'content'  => ($content),
        ]);
    }

    public function script($content, $attr = "")
    {
        $this->head("script", [
            'attr'    => $attr,
            'content' => $content,
        ]);
    }

    public function jsonLd($dataArray, $pretty = true)
    {
        $this->head("script", [
            'attr'    => ' type="application/ld+json"',
            'content' => json_encode($dataArray, $pretty ? JSON_PRETTY_PRINT : 0),
        ]);
    }

    public function __toString()
    {
        return $this->render();
    }

}
