<?php
namespace Kitpages\DataGridBundle\Tests\Grid;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Kitpages\DataGridBundle\KitpagesDataGridEvents;
use Kitpages\DataGridBundle\Event\DataGridEvent;

/**
 * Created by Philippe Le Van.
 * Date: 09/04/13
 */
class ConversionSubscriber
    implements EventSubscriberInterface
{
    private $isDefaultPrevented = false;
    private $afterActivated = false;
    public static function getSubscribedEvents()
    {
        return array(
            KitpagesDataGridEvents::ON_DISPLAY_GRID_VALUE_CONVERSION => 'onConversion',
            KitpagesDataGridEvents::AFTER_DISPLAY_GRID_VALUE_CONVERSION => 'afterConversion'
        );
    }

    public function setIsDefaultPrevented($val) {
        $this->isDefaultPrevented = $val;
    }

    public function setAfterActivated($val) {
        $this->afterActivated = $val;
    }

    public function onConversion(DataGridEvent $event)
    {
        if ($this->isDefaultPrevented) {
            $event->preventDefault();
            $event->set("returnValue", $event->get("field")->getFieldName().';preventDefault;'.$event->get("value"));
        }
        else {
            $row = $event->get("row");
            $event->set("value", $row["node.id"].';'.$event->get("value"));
        }

    }
    public function afterConversion(DataGridEvent $event)
    {
        if (!$this->afterActivated) {
            return;
        }
        $event->set("returnValue", "after;".$event->get("returnValue"));
    }
}