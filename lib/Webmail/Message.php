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

    public function getSubject()
    {
        return $this->webDriver->byId('message-subject')->getText();
    }

    public function getFrom()
    {
        return $this->webDriver->byId('message-from')->getText();
    }

    public function getRecipient()
    {
        return $this->webDriver->byId('message-to')->getText();
    }

    public function getDate()
    {
        return $this->webDriver->byId('message-date')->getText();
    }

    public function selectText()
    {
        $this->webDriver->byId('select-text')->click();
    }

    public function selectHtml()
    {
        $this->webDriver->byId('select-html')->click();
    }
}