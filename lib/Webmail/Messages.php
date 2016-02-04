<?php

namespace Magium\Mail\Webmail;

use Magium\AbstractTestCase;
use Magium\Mail\Configuration;
use Magium\WebDriver\ExpectedCondition;
use Magium\WebDriver\WebDriver;

class Messages
{

    const LOCATOR = 'Magium\Mail\Webmail\Messages';

    protected $webDriver;
    protected $configuration;
    protected $testCase;

    public function __construct(
        WebDriver $webDriver,
        Configuration $configuration,
        AbstractTestCase $testCase
    )
    {
        $this->webDriver = $webDriver;
        $this->configuration = $configuration;
        $this->testCase = $testCase;
    }

    public function open()
    {
        $this->testCase->commandOpen($this->configuration->getWebmailEndpointUrl());
    }

    public function closeMessage()
    {
        $closeElement = $this->webDriver->byId('close-message-window');
        if ($closeElement->isDisplayed()) {
            $closeElement->click();
        }
    }

    /**
     * @param int $number
     * @return \Magium\Mail\Webmail\Message
     */

    public function getMessage($number = 1)
    {
        $this->closeMessage();
        $xpath = sprintf('//*[@id="message-list"]/tr/td[%d]', $number);
        try {
            $element = $this->webDriver->byXpath($xpath);
        } catch (\Exception $e) {
            return null;
        }

        $element->click();
        $this->waitForMessageScreen();
        return new \Magium\Mail\Webmail\Message($this->webDriver);
    }

    /**
     * @param $subject
     * @param int $number
     * @param null $recipient
     * @return \Magium\Mail\Webmail\Message
     */

    public function getMessageWithSubject($subject, $number = 1, $recipient = null)
    {
        $this->closeMessage();
        $xpath = sprintf('//*[@id="message-list"]/tr/td[.="%s" and @class="subject"]', $subject);
        $xpath = $this->addRecipient($xpath, $recipient);
        $xpath = $this->addNumber($xpath, $number);
        try {
            $element = $this->webDriver->byXpath($xpath);
        } catch (\Exception $e) {
            return null;
        }

        $element->click();
        $this->waitForMessageScreen();
        return new \Magium\Mail\Webmail\Message($this->webDriver);
    }

    /**
     * @param $subject
     * @param int $number
     * @param null $recipient
     * @return \Magium\Mail\Webmail\Message
     */

    public function getMessageWithSubjectContains($subject, $number = 1, $recipient = null)
    {
        $this->closeMessage();
        $xpath = sprintf('//*[@id="message-list"]/tr/td[contains(., "%s") and @class="subject"]', $subject);
        $xpath = $this->addRecipient($xpath, $recipient);
        $xpath = $this->addNumber($xpath, $number);
        try {
            $element = $this->webDriver->byXpath($xpath);
        } catch (\Exception $e) {
            return null;
        }

        $element->click();
        $this->waitForMessageScreen();
        return new \Magium\Mail\Webmail\Message($this->webDriver);
    }

    protected function waitForMessageScreen()
    {
        $this->webDriver->wait()->until(ExpectedCondition::visibilityOf($this->webDriver->byId('close-message-window')));
    }

    protected function addRecipient($xpath, $recipient)
    {
        if (!$recipient) return $xpath;
        $xpath .= sprintf('/../td[.="%s" and @class="to"]', $recipient);
        return $xpath;
    }

    protected function addNumber($xpath, $number)
    {
        if ($number == 1) return $xpath;
        $xpath = '(' . $xpath . sprintf(')[%d]', $number);
        return $xpath;
    }

}