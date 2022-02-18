<?php
namespace Accord\Integration\Api\Client;

use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client as GuzzleClient;

/**
 * @method ResponseInterface get($uri, array $options = [])
 * @method ResponseInterface head($uri, array $options = [])
 * @method ResponseInterface put($uri, array $options = [])
 * @method ResponseInterface post($uri, array $options = [])
 * @method ResponseInterface patch($uri, array $options = [])
 * @method ResponseInterface delete($uri, array $options = [])
 */
class Client implements ClientInterface
{
    /**
     * @var string
     */
    private $baseUrl = '';

    /**
     * @var GuzzleClient
     */
    private $guzzle;

    /**
     * @var string
     *
     * Temporary
     * Will be reworked with OPEQE-108
     */
    private $apiType;

    /**
     * @param ConfigInterface $config
     */
    public function init(ConfigInterface $config)
    {
        $this->baseUrl = $config->getRestApiEndpoint();
        $this->apiType = $config->getApiType();

        $this->guzzle = new GuzzleClient([
            'handler' => $config->getHandler(),
            /**
             * @auth
             *  $encodedString = base64_encode($apiUsername . ':' . $apiPassword);
             *  headers' => [
             *      'Authorization' => 'Basic ' . $encodedString,
             *  ]
             */
            'auth' => [
                $config->getRestApiUsername(),
                $config->getRestApiPassword(),
            ],
        ]);
    }

    /**
     * @param string $method
     * @param array $args
     * @throws \InvalidArgumentException
     * @throws ClientException
     * @return $this
     * @return ResponseInterface
     */
    public function __call($method, $args)
    {
        if (count($args) < 1) {
            throw new \InvalidArgumentException('Magic request methods require a URI and optional options array');
        }

        $args[0] = $this->applyUrl($args[0]);

        try {
            return $this->guzzle->__call($method, $args);
        } catch (GuzzleRequestException $e) {
            throw new ClientException($e->getMessage(), $e->getRequest(), $e->getResponse());
        }
    }

    /**
     * @return string|null
     */
    public function getApiType(): ?string
    {
        return $this->apiType;
    }

    /**
     * @debug
     * @return string
     */
    protected function getXDebug()
    {
        if ($this->baseUrl == 'http://accord.loc') {
            if (isset($_SERVER['XDEBUG_CONFIG'])) {
                return 'XDEBUG_SESSION_START=PHPSTORM';
            }
            if (!isset($_COOKIE['XDEBUG_SESSION'])) {
                // switch xdebug
                return 'XDEBUG_SESSION_START=PHPSTORM';
            }
        }
        return '';
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function applyUrl($uri)
    {
        $separator = strpos($this->baseUrl . '/' . $uri, '?') ? '&' : '?';
        return $this->baseUrl . '/' . $uri . $separator . $this->getXDebug();
    }
}
