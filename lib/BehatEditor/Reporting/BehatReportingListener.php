<?php

namespace BehatEditor\Reporting;

use BehatEditor\BehatEditorTraits;
use BehatEditor\BehatYmlMangler;
use BehatWrapper\Event\BehatEvent;
use BehatWrapper\Event\BehatSuccessListenerInterface;

class BehatReportingListener extends ReportingBase implements BehatSuccessListenerInterface  {
    use BehatEditorTraits, BehatYmlMangler;



    /**
     * @param $event BehatEvent
     */
    public function handleSuccess($event) {
        $this->event = $event;
        $this->optionsFromYaml();
        $this->setOutput();
        $this->setJobUuid();
        $this->setBranch();
        $this->setFileName();
        $this->setRepoName();
        $this->setRemoteJobId();
        $this->setTagsValue();
        $this->setCustomDataValue();
        $this->reportDataValueStatus();
    }

    public function reportDataValueStatus()
    {
        $this->data_values['status'] = 1;
    }

}
