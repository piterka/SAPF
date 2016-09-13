<?php

namespace SAPF\Event;

interface EventDispatcherInterface
{

    public function dispatch($eventName, Event $event = null);

    //
    public function getListenerPriority($eventName, $listener);

    public function getListeners($eventName);

    public function addListener($eventName, $listener, $priority = 0);

    public function hasListeners($eventName);

    public function removeListener($eventName, $listener);
}
