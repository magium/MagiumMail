<?php

namespace Magium\Mail\Webmail;

use Magium\WebDriver\WebDriver;

class Message
{
    protected $webDriver;

    public function __construct(
        WebDriver $webDriver
    )
    {
        $this->webDriver = $webDriver;
    }

    /**
     * Returns the subject of the current message
     *
     * @return string
     */

    public function getSubject()
    {
        return $this->webDriver->byId('message-subject')->getText();
    }

    /**
     * Retrieves the email sender
     *
     * @return string
     */

    public function getFrom()
    {
        return $this->webDriver->byId('message-from')->getText();
    }

    /**
     * Returns the email recipient
     *
     * @return string
     */

    public function getRecipient()
    {
        return $this->webDriver->byId('message-to')->getText();
    }

    /**
     * Retreives the date that the message was received.
     *
     * @return string
     */

    public function getDate()
    {
        return $this->webDriver->byId('message-date')->getText();
    }

    /**
     * Selects the text/plain version of the message
     */

    public function selectText()
    {
        $this->webDriver->byId('select-text')->click();
    }

    /**
     * Selects the text/html version of the message
     */

    public function selectHtml()
    {
        $this->webDriver->byId('select-html')->click();
    }
}