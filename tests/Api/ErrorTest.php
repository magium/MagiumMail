<?php

namespace Test\Api;

use Magium\Mail\Api\Generator;
use Magium\Mail\Configuration;
use Magium\Mail\Api\InvalidResponseException;
use Zend\Http\Headers;
use Zend\Http\Response;

class ErrorTest extends \PHPUnit_Framework_TestCase
{

    public function testFailedSendThrowsInvalidRequest()
    {
        $builder = $this->getMockBuilder('Zend\Http\Client')->setMethods(['send']);
        $client = $builder->getMock();
        $client->expects($this->once())->method('send')->willThrowException(new InvalidResponseException());

        $this->setExpectedException('Magium\Mail\Api\InvalidResponseException');
        $generator = new Generator(new Configuration(), $client, 'abcd');
        $generator->generate();

    }

    public function testErrorMessageThrowsErrorException()
    {
        $this->setExpectedException('Magium\Mail\Api\ErrorException');
        $builder = $this->getMockBuilder('Zend\Http\Client')->setMethods(['send']);
        $client = $builder->getMock();
        /* @var $client \Zend\Http\Client */
        $response = new Response();
        $response->setContent(json_encode(['error' => 'error message']));
        $response->setHeaders(Headers::fromString('Content-Type: application/json'));
        $client->expects($this->once())->method('send')->willReturn($response);

        $generator = new Generator(new Configuration(), $client, 'abcd');
        $generator->generate();

    }

    public function testWrongContentTypeThrowsInvalidResponseException()
    {
        $this->setExpectedException('Magium\Mail\Api\InvalidResponseException');
        $builder = $this->getMockBuilder('Zend\Http\Client')->setMethods(['send']);
        $client = $builder->getMock();
        /* @var $client \Zend\Http\Client */
        $response = new Response();
        $response->setContent(json_encode(['error' => 'error message']));
        $response->setHeaders(Headers::fromString('Content-Type: wrong'));
        $client->expects($this->once())->method('send')->willReturn($response);

        $generator = new Generator(new Configuration(), $client, 'abcd');
        $generator->generate();

    }

}