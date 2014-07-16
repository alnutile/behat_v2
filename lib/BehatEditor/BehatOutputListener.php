<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/15/14
 * Time: 8:03 PM
 */

namespace BehatEditor;


use BehatWrapper\Event\BehatOutputEvent;
use BehatWrapper\Event\BehatOutputListenerInterface;

class BehatOutputListener implements BehatOutputListenerInterface {

    protected $output;

    public function handleOutput(BehatOutputEvent $event)
    {

        while($event->getBuffer()) {
            $this->setOutput($event->getBuffer());
            return $event->getBuffer();
        }
    }

    public function setOutput($output)
    {
        $this->output[] = $output;
        return $this;
    }

    public function getOutput()
    {
        return $this->output;
    }
} 