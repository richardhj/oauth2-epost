<?php
/**
 * E-POSTBUSINESS API integration for Contao Open Source CMS
 *
 * Copyright (c) 2015-2016 Richard Henkenjohann
 *
 * @package E-POST
 * @author  Richard Henkenjohann <richard-epost@henkenjohann.me>
 */

namespace EPost\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\RequiredParameterTrait;
use Psr\Http\Message\ResponseInterface;


/**
 * Class EPost
 * @package EPost\OAuth2\Client\Provider
 */
class EPost extends AbstractProvider
{

    use RequiredParameterTrait;


    /**
     * The login endpoint for production
     *
     * @var string
     */
    protected static $endpointProduction = 'https://login.epost.de';


    /**
     * The login endpoint for test and integration environment
     *
     * @var string
     */
    protected static $endpointTest = 'https://login.epost-gka.de';


    /**
     * A toggle to enable test and integration environment
     *
     * @var bool
     */
    protected $enableTestEnvironment;


    /**
     * An array containing the scopes used for authentication
     *
     * @var array
     */
    protected $scopes;


    /**
     * The content of the license file (LIF)
     *
     * @var string
     */
    protected $lif;


    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->checkRequiredParameters($this->getRequiredOptions(), $options);

        $possible = $this->getConfigurableOptions();
        $configured = array_intersect_key($options, array_flip($possible));

        foreach ($configured as $key => $value) {
            $this->$key = $value;
        }

        // Remove all options that are only used locally
        $options = array_diff_key($options, $configured);

        parent::__construct($options, $collaborators);
    }


    /**
     * {@inheritdoc}
     */
    public function getBaseAuthorizationUrl()
    {
        return (!$this->enableTestEnvironment ? static::$endpointProduction : static::$endpointTest).'/oauth2/auth';
    }


    /**
     * {@inheritdoc}
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return (!$this->enableTestEnvironment ? static::$endpointProduction : static::$endpointTest).'/oauth2/tokens/';
    }


    /**
     * {@inheritdoc}
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        throw new \BadFunctionCallException('A resource owner is not supported by the E-POST OAuth implementation');
    }


    /**
     * {@inheritdoc}
     */
    protected function getDefaultScopes()
    {
        return $this->scopes;
    }


    /**
     * {@inheritdoc}
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }


    /**
     * Builds request options used for requesting an access token including the client authentication header
     *
     * @param  array $params
     *
     * @return array
     */
    protected function getAccessTokenOptions(array $params)
    {
        // Add params that are required
        $params += [
            'scope' => implode($this->getScopeSeparator(), $this->getDefaultScopes()),
        ];

        $options = parent::getAccessTokenOptions($params);

        // Add authorization header
        $options['headers']['Authorization'] = sprintf(
            'Basic %s',
            base64_encode(
                sprintf(
                    '%s:%s',
                    $this->clientId,
                    $this->lif
                )
            )
        );

        return $options;
    }


    /**
     * {@inheritdoc}
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            throw new IdentityProviderException($data['error'], $response->getStatusCode(), $data);
        }
    }


    /**
     * {@inheritdoc}
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        throw new \BadFunctionCallException('A resource owner is not supported by the E-POST OAuth implementation');
    }


    /**
     * Returns all options that can be configured
     *
     * @return array
     */
    protected function getConfigurableOptions()
    {
        return array_merge(
            $this->getRequiredOptions(),
            [
                'enableTestEnvironment',
            ]
        );
    }


    /**
     * Returns all options that are required
     *
     * @return array
     */
    protected function getRequiredOptions()
    {
        return [
            'clientId',
            'lif',
            'scopes',
        ];
    }
}
