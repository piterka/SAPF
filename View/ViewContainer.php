<?php

namespace SAPF\View;

class ViewContainer extends \SAPF\DI\Container
{

    protected $_pathPrepend = "";
    protected $_decorChain  = FALSE;

    /**
     * Gets path prepended to view name
     * @return string
     */
    public function getPath()
    {
        return $this->_pathPrepend;
    }

    /**
     * Sets path prepended to view name
     * @param string $path
     * @return \SAPF\View\View
     */
    public function setPath($path)
    {
        $this->_pathPrepend = $path;
        return $this;
    }

    /**
     * Gets view decor chain
     * @return \SAPF\Decor\DecorChain Decor chain
     */
    public function getDecorChain()
    {
        if (!$this->_decorChain) {
            $this->_decorChain = new \SAPF\Decor\DecorChain();
        }
        return $this->_decorChain;
    }

    /**
     * Returns TRUE if view file exists
     * @param string $view
     * @return boolean
     */
    public function viewExists($view)
    {
        return file_exists($this->viewFile($view));
    }

    /**
     * Returns view filePath
     * @param string $view
     * @return string
     */
    public function viewFile($view)
    {
        return $this->_pathPrepend ? \SAPF\Util\Filesystem::file($this->_pathPrepend, $view . '.php') : ($view . '.php');
    }

    /**
     * Renders specified view
     * @param string $view
     * @return string Rendered and decorated content
     * @throws \LogicException
     */
    public function render($view, $decorateOutput = true)
    {
        $viewFile = $this->viewFile($view);

        if (!file_exists($viewFile)) {
            throw new \LogicException("View file: " . $viewFile . " doesn't exist!");
        }

        ob_start();
        include( $viewFile );
        $content = ob_get_contents();
        ob_end_clean();

        if ($decorateOutput && $this->_decorChain) {
            $content = $this->_decorChain->decorate($content);
        }

        return $content;
    }
}
