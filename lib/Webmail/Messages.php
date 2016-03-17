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
    protected $timeout = 30;

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

    /**
     * This method will open the API endpoint in a browser, but will not navigate to any particular message.  Use one
     * of the getMessage*() methods to retrieve the message.
     */

    public function open()
    {
        $this->testCase->commandOpen($this->configuration->getWebmailEndpointUrl());
    }

    /**
     * Gets the current message retrieval timeout.  The default is 30 seconds but some transactional emails, notably
     * later versions of Magento because they are sent via cron, may require longer timeouts.
     *
     * @return int
     */
    public function getMessageTimeout()
    {
        return $this->timeout;
    }

    /**
     * Sets the message retrieval timeout.
     *
     * @param int $timeout
     */
    public function setMessageTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Closes the current message, if a message is displayed. This is used to allow you to retrieve other messages
     * from the API.  It does not close the API window.
     */

    public function closeMessage()
    {
        $closeElement = $this->webDriver->byId('close-message-window');
        if ($closeElement->isDisplayed()) {
            $closeElement->click();
        }
    }

    /**
     * Retrieves the nth message in the list.  Will automatically close the message window if one is open.
     *
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
     * Retrieves a message that has an exact subject.
     *
     * @param string $subject The subject of the email you wish to retrieve
     * @param int $number The nth message with that subject.  Defaults to the first
     * @param null $recipient The recipient of the message
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
     * Retrieves a message that contains a certain text string in the subject.  A good example of this would be the
     * order ID retrieved from the OrderId extractor
     *
     * @param string $subject The string to search for in the subject
     * @param int $number The nth message to select, defaults to the first
     * @param null $recipient Additional recipient filter.
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

    /**
     * This method will wait until the message window is open, indicating that the message has been loaded.  This timeout
     * could be immediate or several minutes, depending on the maximum expected elapsed time from when the email is
     * requested to when it is sent.
     *
     */

    protected function waitForMessageScreen()
    {
        $this->webDriver->wait($this->getMessageTimeout())->until(ExpectedCondition::visibilityOf($this->webDriver->byId('close-message-window')));
    }

    /**
     * Returns prepared Xpath to add a recipient match to the current Xpath string.
     *
     * @param $xpath string The string to add the xpath to
     * @param $recipient string the recipient of the email
     * @return string The fully appended Xpath string
     */

    protected function addRecipient($xpath, $recipient)
    {
        if (!$recipient) return $xpath;
        $xpath .= sprintf('/../td[.="%s" and @class="to"]', $recipient);
        return $xpath;
    }

    /**
     * Adds an nth selector to the current Xpath
     *
     * @param $xpath string the Xpath to wrap the nth selector to
     * @param $number int which nth selector you want to use
     * @return string The final Xpath
     */

    protected function addNumber($xpath, $number)
    {
        if ($number == 1) return $xpath;
        $xpath = '(' . $xpath . sprintf(')[%d]', $number);
        return $xpath;
    }

}