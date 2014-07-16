<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/16/14
 * Time: 1:06 PM
 */

namespace BehatEditor;

use BehatWrapper\Event\BehatSuccessListenerInterface;
use BehatEditor\SauceLabsBase;

/**
 * @TODO set tags and custom data as needed
 *
 * Class SauceLabsSuccessListener
 * @package BehatEditor
 */
class SauceLabsSuccessListener  extends SauceLabsBase implements BehatSuccessListenerInterface  {

    public function handleSuccess($event)
    {
        $this->setStatus(1);
        $this
            ->updateStatus($event)
            ->updateTags($event)
            ->updateCustomData($event);
    }

} 