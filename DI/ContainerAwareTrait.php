<?php

namespace SAPF\DI;

trait ContainerAwareTrait
{
    /**
     * @var \SAPF\DI\ContainerInterface
     */
    protected $_container = FALSE;

    /**
     * Sets container to object
     * @param ContainerInterface $container
     */
    public function setContainer(\SAPF\DI\ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * Gets container
     * @return ContainerInterface Container
     */
    public function getContainer()
    {
        return $this->_container;
    }

}
