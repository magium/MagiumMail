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

    /**
     * Retrieves the pseudo first name
     *
     * @return mixed
     */

    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Retrieves the pseudo last name
     *
     * @return mixed
     */


    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Sets the pseudo first name
     *
     * @param string $value
     */

    public function setFirstName($value)
    {
        $this->firstName = $value;
    }

    /**
     * Sets the pseudo last name
     *
     * @param string $value
     */


    public function setLastName($value)
    {
        $this->lastName = $value;
    }

    /**
     * Allows you to set an arbitrary API key
     *
     * @param $key
     */

    public function setApiKey($key)
    {
        $this->configuration->setApiKey($key);
    }

    /**
     * Creates a new, unique email address from Magium Mail.
     *
     * @param string $domain Used in core Magium, ignored in Magium mail
     * @return string The new email address
     * @throws ErrorException If a server error occurred
     * @throws InvalidResponseException If the email address is not provided by the server
     */

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