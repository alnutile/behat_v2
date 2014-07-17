<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/14/14
 * Time: 11:59 AM
 */

namespace BehatEditor;

use BehatEditor\BehatSetNewNameOnYaml;
use BehatWrapper\Event\BehatPrepareEvent;
use BehatWrapper\Event\BehatPrepareListenerInterface;

class BehatPrepareListener implements BehatPrepareListenerInterface{

    /**
     * @var
     */
    private $event;

    /**
     * @var \BehatEditor\BehatSetNewNameOnYaml
     */
    private $behatSetNewNameOnYaml;

    public function __construct(BehatSetNewNameOnYaml $behatSetNewNameOnYaml)
    {

        $this->behatSetNewNameOnYaml = $behatSetNewNameOnYaml;
    }

    public function handlePrepare(BehatPrepareEvent $event)
    {
        $this->event = $event;
        $this->behatSetNewNameOnYaml->setEvent($event)->setName($event->getWrapper()->getUuid());
    }


} 