<?php

namespace SAPF\View;

interface ViewContainerAwareInterface
{

    /**
     * Sets view container
     * @param \SAPF\View\View $v
     */
    public function setViewContainer(ViewContainer $v);
}
