<?php

declare(strict_types=1);

namespace Accord\Integration\Api\Commands;

use Accord\Integration\Api\Client\ClientInterface;
use Accord\Integration\Api\Request\RequestInterface;
use Accord\Integration\Api\Response\ResponseInterface;
use Accord\Integration\Model\Config\Source\ApiType;

abstract class Command implements CommandInterface
{
    protected string $url = '';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param ClientInterface $client
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(ClientInterface $client, RequestInterface $request, ResponseInterface $response)
    {
        $this->client = $client;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return (new \ReflectionClass(get_called_class()))->getShortName();
    }

    /**
     * @return ClientInterface
     */
    final public function getClient()
    {
        return $this->client;
    }

    /**
     * @return RequestInterface
     */
    final public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    final public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    final public function getUrl(): string
    {
        switch ($this->client->getApiType()) {
            case ApiType::TYPE_SAGE:
                $urlPart = 'S200connect/';
                break;
            case ApiType::TYPE_ACCORD:
                // no break
            default:
                $urlPart = 'magento/';
        }

        return trim($urlPart . $this->url, '/');
    }

    /**
     * @param null $data
     * @return ResponseInterface
     */
    public function execute($data = null)
    {
        if ($data) {
            $this->getRequest()->setData($data);
        }

        $psrResponse = $this->getClient()->post(
            $this->getUrl(),
            [
                'json' => $this->getRequest()->getData(),
                'headers' => $this->getRequest()->getHeaders(),
            ]
        );

        return $this->getResponse()->setResponse($psrResponse);
    }
}
