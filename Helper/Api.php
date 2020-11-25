<?php

namespace RealtimeDespatch\OrderFlow\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use SixBySix\RealtimeDespatch\Api\Credentials;

/**
 * API Helper.
 */
class Api extends AbstractHelper
{
    /**
     * Returns the API endpoint.
     *
     * @param integer|null $scopeId
     *
     * @return string
     */
    public function getEndpoint($scopeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            'orderflow_api/settings/endpoint',
            ScopeInterface::SCOPE_WEBSITE,
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
            ScopeInterface::SCOPE_WEBSITE,
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
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the API organisation.
     *
     * @param integer|null $scopeId
     *
     * @return string
     */
    public function getOrganisation($scopeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            'orderflow_api/settings/organisation',
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the API channel.
     *
     * @param integer|null $scopeId
     *
     * @return string
     */
    public function getChannel($scopeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            'orderflow_api/settings/channel',
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
    }

    /**
     * Returns the API Credentials.
     *
     * @param integer|null $scopeId
     *
     * @return Credentials
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
