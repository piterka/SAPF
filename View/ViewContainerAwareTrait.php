<?php

namespace SAPF\View;

trait ViewContainerAwareTrait
{

    protected $_v_c;

    /**
     * Sets view container
     * @param \SAPF\View\ViewContainer $vc
     */
    public function setViewContainer(\SAPF\View\ViewContainer $vc)
    {
        $this->_v_c = $vc;
        return $this;
    }

    /**
     * Gets view container
     * @return \SAPF\View\ViewContainer
     */
    public function getViewContainer()
    {
        return $this->_v_c;
    }

}
