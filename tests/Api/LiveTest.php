<?php

namespace Test\Api;

use Magium\AbstractTestCase;
use Magium\Mail\Api\Generator;

class LiveTest extends AbstractTestCase
{
    public function testLiveSite()
    {
        $generator = $this->get(Generator::LOCATOR);
        /* @var $generator Generator */
        $generator->setFirstName('Test');
        $generator->setLastName('User');
        $generator->setApiKey('BxG1LbilkKDgJxEGH3iWGp1DbwpxRqkUJCp1rypPU9E1NmIxMmEwYThiMTg0');
        $email = $generator->generate();
        self::assertContains('magium', $email);
    }
}
