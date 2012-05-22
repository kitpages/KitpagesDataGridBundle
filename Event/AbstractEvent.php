<?php
namespace Kitpages\DataGridBundle\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    /** @var array */
    protected $data = array();
    /** @var bool */
    protected $isDefaultPrevented = false;
    /** @var bool */
    protected $isPropagationStopped = false;
    
    public function preventDefault()
    {
        $this->isDefaultPrevented = true;
    }
    
    public function isDefaultPrevented()
    {
        return $this->isDefaultPrevented;
    }

    public function stopPropagation()
    {
        $this->isPropagationStopped = true;
    }

    public function isPropagationStopped()
    {
        return $this->isPropagationStopped;
    }
    
    public function set($key, $val)
    {
        $this->data[$key] = $val;
    }
    
    public function get($key)
    {
        if (!array_key_exists($key, $this->data)) {
            return null;
        }
        return $this->data[$key];
    }

}
