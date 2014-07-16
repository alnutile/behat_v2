<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/16/14
 * Time: 1:06 PM
 */

namespace BehatEditor;

use BehatWrapper\Event\BehatErrorListenerInterface;
use BehatEditor\SauceLabsBase;

/**
 * @TODO set tags and custom data as needed
 *
 * Class SauceLabsSuccessListener
 * @package BehatEditor
 */
class SauceLabsErrorListener  extends SauceLabsBase implements BehatErrorListenerInterface  {

    public function handleError($event)
    {
        $this->setStatus(0);
        $this
            ->updateStatus($event)
            ->updateTags($event)
            ->updateCustomData($event);
    }
} 