<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/16/14
 * Time: 1:06 PM
 */

namespace BehatEditor\SauceLabs;

use BehatWrapper\Event\BehatSuccessListenerInterface;
use BehatEditor\SauceLabs\SauceLabsBase;

/**
 * @TODO set tags and custom data as needed
 *
 * Class SauceLabsSuccessListener
 * @package BehatEditor
 */
class SauceLabsSuccessListener  extends SauceLabsBase implements BehatSuccessListenerInterface  {

    public function handleSuccess($event)
    {
        $this->event_object = $event;
        $this->setStatus(1);
        $this
            ->updateStatus($event)
            ->updateTags($event)
            ->updateCustomData($event);
    }

} 