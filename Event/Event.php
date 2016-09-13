<?php

namespace SAPF\Event;

class Event
{

    protected $_isCancelled = false;

    public function isCancelled()
    {
        return $this->_isCancelled;
    }

    public function setCancelled($canceled = true)
    {
        $this->_isCancelled = $canceled;
        return $this;
    }

}
