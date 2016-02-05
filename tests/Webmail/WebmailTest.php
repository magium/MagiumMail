<?php

namespace Test\Webmail;

use Magium\AbstractTestCase;
use Magium\Mail\Webmail\Messages;

class WebmailTest extends AbstractTestCase
{

    protected $subjectContains = '<subject contains something>';
    protected $recipient = '<magium mail recipient';
    protected $subjectEquals = '<full subject >';

    public function setUp()
    {
        self::markTestSkipped('This test requires individual configuration');
        parent::setUp();
    }

    public function testNavigateByNumber()
    {
        $messages = $this->get(Messages::LOCATOR);
        /* @var $messages Messages */
        $messages->open();
        $message = $messages->getMessage();
        self::assertNotNull($message->getSubject());

        $message = $messages->getMessage(2);
        self::assertNotNull($message->getSubject());
    }

    public function testNavigateSubjectContains()
    {
        $messages = $this->get(Messages::LOCATOR);
        /* @var $messages Messages */
        $messages->open();
        $message = $messages->getMessageWithSubjectContains($this->subjectContains);
        self::assertNotNull($message->getSubject());
    }

    public function testNavigateSubjectEquals()
    {
        $messages = $this->get(Messages::LOCATOR);
        /* @var $messages Messages */
        $messages->open();
        $message = $messages->getMessageWithSubject($this->subjectEquals);
        self::assertNotNull($message->getSubject());
    }

    public function testNavigateSubjectContainsAndRecipient()
    {
        $messages = $this->get(Messages::LOCATOR);
        /* @var $messages Messages */
        $messages->open();
        $message = $messages->getMessageWithSubjectContains($this->subjectContains, 1, $this->recipient);
        self::assertNotNull($message->getSubject());
    }

    public function testNavigateSubjectEqualsAndRecipient()
    {
        $messages = $this->get(Messages::LOCATOR);
        /* @var $messages Messages */
        $messages->open();
        $message = $messages->getMessageWithSubject($this->subjectEquals, 1, $this->recipient);
        self::assertNotNull($message->getSubject());
    }

    public function testNavigateSubjectContainsAndInvalidRecipientDoesNotDisplay()
    {
        $messages = $this->get(Messages::LOCATOR);
        /* @var $messages Messages */
        $messages->open();
        $message = $messages->getMessageWithSubjectContains($this->subjectContains, 1, 'nobody');
        self::assertNull($message);
    }

}