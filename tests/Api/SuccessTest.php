<?php

namespace Test\Api;

use Magium\Mail\Api\Generator;
use Magium\Mail\Configuration;
use Zend\Http\Headers;
use Zend\Http\Response;

class SuccessTest extends \PHPUnit_Framework_TestCase
{

    public function testUrlIsRight()
    {
        $builder = $this->getMockBuilder('Zend\Http\Client')->setMethods(['send']);
        $client = $builder->getMock();
        /* @var $client \Zend\Http\Client */
        $response = new Response();
        $response->setContent(json_encode(['email' => 'test@example.com']));
        $response->setHeaders(Headers::fromString('Content-Type: application/json'));
        $client->expects($this->once())->method('send')->willReturn($response);

        $configuration = new Configuration();
        $generator = new Generator($configuration, $client, 'abcd');
        $email = $generator->generate();

        self::assertEquals('test@example.com', $email);
        self::assertEquals($configuration->getApiEndpointUrl(), $client->getUri()->toString());
    }

}