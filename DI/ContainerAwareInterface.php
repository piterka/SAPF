<?php

namespace SAPF\DI;

interface ContainerAwareInterface
{

    /**
     * Set container to object
     * @param \SAPF\DI\ContainerInterface $container
     */
    public function setContainer(\SAPF\DI\ContainerInterface $container);
}
