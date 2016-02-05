<?php

namespace Magium\Mail;

use Magium\AbstractConfigurableElement;
use Magium\Mail\Api\MissingAPIKeyException;
use Zend\Uri\Http;

class Configuration extends AbstractConfigurableElement
{

    const LOCATOR = 'Magium\Mail\Configuration';

    protected $apiKey;
    protected $apiEndpointUrl = 'https://magiumlib.com/mail/api/generateAccount';
    protected $webmailEndpointUrl = 'https://magiumlib.com/mail/webmail';

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getApiEndpointUrl()
    {
        return $this->buildUrl($this->apiEndpointUrl);
    }

    /**
     * @return string
     */
    public function getWebmailEndpointUrl()
    {
        return $this->buildUrl($this->webmailEndpointUrl);
    }

    protected function buildUrl($url)
    {
        if (!$this->apiKey) {
            throw new MissingAPIKeyException('Missing the API key.  Please either call setApiKey(), set the API key as a dependency parameter, or create a Magium/Mail/GeneratorConfiguration.php file to set the API key');
        }
        $uri = new Http($url);
        $uri->setQuery(['key' => $this->apiKey]);
        return $uri->toString();
    }

    /**
     * @param string $apiEndpointUrl
     */
    public function setApiEndpointUrl($apiEndpointUrl)
    {
        $this->apiEndpointUrl = $apiEndpointUrl;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $webmailEndpointUrl
     */
    public function setWebmailEndpointUrl($webmailEndpointUrl)
    {
        $this->webmailEndpointUrl = $webmailEndpointUrl;
    }



}