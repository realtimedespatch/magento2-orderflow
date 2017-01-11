<?php

namespace RealtimeDespatch\OrderFlow\Helper;

use \SixBySix\RealtimeDespatch\Api\Credentials;

/**
 * API Helper.
 */
class Api extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Returns the API endpoint.
     *
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function getEndpoint($scopeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            'orderflow_api/settings/endpoint',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the API username.
     *
     * @param integer|null $scopeId
     *
     * @return string
     */
    public function getUsername($scopeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            'orderflow_api/settings/username',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the API password.
     *
     * @param integer|null $scopeId
     *
     * @return string
     */
    public function getPassword($scopeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            'orderflow_api/settings/password',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the API organisation.
     *
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function getOrganisation($scopeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            'orderflow_api/settings/organisation',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the API channel.
     *
     * @param integer|null $scopeId
     *
     * @return boolean
     */
    public function getChannel($scopeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            'orderflow_api/settings/channel',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the API Credentials.
     *
     * @param integer|null $scopeId
     *
     * @return \SixBySix\RealtimeDespatch\Api\Credentials
     */
    public function getCredentials($scopeId = null)
    {
        $credentials  = new Credentials();
        $credentials->setEndpoint($this->getEndpoint($scopeId));
        $credentials->setUsername($this->getUsername($scopeId));
        $credentials->setPassword($this->getPassword($scopeId));
        $credentials->setOrganisation($this->getOrganisation($scopeId));
        $credentials->setChannel($this->getChannel($scopeId));

        return $credentials;
    }
}