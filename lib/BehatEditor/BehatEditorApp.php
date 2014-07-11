<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 7/11/14
 * Time: 11:32 AM
 */

namespace BehatEditor;

use BehatWrapper\BehatWrapper;

class BehatEditorApp {

    /**
     * @var \BehatWrapper\BehatWrapper
     */
    private $behatWrapper;

    public function __construct(BehatWrapper $behatWrapper = null)
    {

        $this->behatWrapper = ($behatWrapper == null) ? new BehatWrapper() : $behatWrapper;
    }

} 