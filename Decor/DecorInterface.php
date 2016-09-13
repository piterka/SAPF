<?php

namespace SAPF\Decor;

interface DecorInterface
{

    /**
     * Decorates subject
     * @param mixed $subject
     * @return mixed Decorated subject
     */
    public function decorate($subject);
}
