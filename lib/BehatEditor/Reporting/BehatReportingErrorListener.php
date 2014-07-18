<?php

namespace BehatEditor\Reporting;

use BehatEditor\BehatEditorTraits;
use BehatEditor\BehatYmlMangler;
use BehatWrapper\Event\BehatErrorListenerInterface;
use BehatWrapper\Event\BehatEvent;

class BehatReportingErrorListener extends ReportingBase implements BehatErrorListenerInterface  {
    use BehatEditorTraits, BehatYmlMangler;

    /**
     * @param $event BehatEvent
     */
    public function handleError($event) {
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
        $this->data_values['status'] = '0';
    }

}
