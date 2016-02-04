<?php

namespace Magium\Mail\Api;

use Magium\Identities\NameInterface;
use Magium\Mail\Configuration;
use Zend\Http\Client;

class Generator extends \Magium\Util\EmailGenerator\Generator implements NameInterface
{
    const LOCATOR = 'Magium\Mail\Api\Generator';

    protected $configuration;
    protected $client;
    protected $firstName;
    protected $lastName;

    public function __construct(
        Configuration $configuration,
        Client $client,
        $apiKey = null
    )
    {
        $this->client = $client;
        $this->configuration = $configuration;
        if ($apiKey !== null) {
            $configuration->setApiKey($apiKey);
        }
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setFirstName($value)
    {
        $this->firstName = $value;
    }

    public function setLastName($value)
    {
        $this->lastName = $value;
    }


    public function setApiKey($key)
    {
        $this->configuration->setApiKey($key);
    }

    public function generate($domain = null)
    {
        $this->client->setUri($this->configuration->getApiEndpointUrl());
        try {
            $this->client->setMethod('post');
            $name = [];
            if ($this->firstName) {
                $name[] = $this->firstName;
            }
            if ($this->lastName) {
                $name[] = $this->lastName;
            }

            if ($name) {
                $this->client->setParameterPost(['name' => implode(' ', $name)]);
            }
            $response = $this->client->send();
            $contentType = $response->getHeaders()->get('content-type')->getFieldValue();

        } catch (\Exception $e) {
            throw new InvalidResponseException($e->getMessage());
        }
        if (stripos($contentType, 'application/json') !== 0) {
            throw new InvalidResponseException(sprintf('Server content type was "%s".  Expected application/json.', $contentType));
        }
        $json = json_decode($response->getBody(), true);
        if (isset($json['error'])) {
            throw new ErrorException($json['error']);
        } else if (!isset($json['email'])) {
            throw new InvalidResponseException('Missing email response');
        }
        return $json['email'];

    }

}