<?php
namespace Accord\Integration\Api\Client;

use Psr\Http\Message\ResponseInterface;

/**
 * @method ResponseInterface get($uri, array $options = [])
 * @method ResponseInterface head($uri, array $options = [])
 * @method ResponseInterface put($uri, array $options = [])
 * @method ResponseInterface post($uri, array $options = [])
 * @method ResponseInterface patch($uri, array $options = [])
 * @method ResponseInterface delete($uri, array $options = [])
 */
interface ClientInterface
{
    /**
     * @param ConfigInterface $config
     */
    public function init(ConfigInterface $config);

}