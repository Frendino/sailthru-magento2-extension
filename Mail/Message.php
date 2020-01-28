<?php

namespace Sailthru\MageSail\Mail;

class Message extends \Magento\Framework\Mail\Message
{
    /**
     * Template info.
     *
     * @var array
     **/
    private $templateInfo = [];

    /**
     * To get info about template.
     *
     * @return array
     **/
    public function getTemplateInfo()
    {
        return $this->templateInfo;
    }

    /**
     * To set info about template.
     *
     * @param array $info
     *
     * @return bool
     **/
    public function setTemplateInfo(array $info)
    {
        if ($info) {
            $this->templateInfo = $info;

            return true;
        }

        return false;
    }

    /**
     * Get decoded MIME body text
     *
     * @return string
     */
    public function getDecodedBodyText()
    {
        return !empty($this->getBody()) && !empty($this->getBody()->getParts()[0])
            ? $this->getBody()->getParts()[0]->getRawContent()
            : '';
    }
}
