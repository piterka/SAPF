<?php

namespace SAPF\Event;

class EventDispatcher implements EventDispatcherInterface
{

    protected $_listeners = array();

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->_listeners[$eventName][$priority][] = $listener;
        return $this;
    }

    public function dispatch($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        if ($listeners = $this->getListeners($eventName)) {
            foreach ($listeners as $listener) {
                call_user_func($listener, $event, $eventName, $this);
            }
        }

        return $event;
    }

    public function getListeners($eventName)
    {
        if (!isset($this->_listeners[$eventName])) {
            return array();
        }

        // sort by priority
        krsort($this->_listeners[$eventName]);
        
        return call_user_func_array('array_merge', $this->_listeners[$eventName]);
    }

    public function getListenerPriority($eventName, $listener)
    {
        if (!$this->hasListeners($eventName)) {
            return null;
        }

        foreach ($this->_listeners[$eventName] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                return $priority;
            }
        }
    }

    public function hasListeners($eventName)
    {
        return (bool) count($this->getListeners($eventName));
    }

    public function removeListener($eventName, $listener)
    {
        if (!$this->hasListeners($eventName)) {
            return $this;
        }

        foreach ($this->_listeners[$eventName] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                unset($this->_listeners[$eventName][$priority][$key]);
            }
        }
        return $this;
    }

}
