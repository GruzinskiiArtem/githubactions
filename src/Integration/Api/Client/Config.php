<?php
namespace Accord\Integration\Api\Client;

use GuzzleHttp\HandlerStack;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use Psr\Log\LoggerInterface;

class Config implements ConfigInterface
{

    /**
     * @var LoggerInterface;
     */
    private $logger;

    /**
     * @var string
     */
    protected $apiEndpoint;

    /**
     * @var string
     */
    protected $apiUsername;

    /**
     * @var string
     */
    protected $apiPassword;


    /**
     * @var string
     */
    protected $apiType;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        foreach ($params as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * @inheritDoc
     */
    public function getRestApiEndpoint(): ?string
    {
        return $this->apiEndpoint;
    }

    /**
     * @inheritDoc
     */
    public function getRestApiUsername(): ?string
    {
        return $this->apiUsername;
    }

    /**
     * @inheritDoc
     */
    public function getApiType(): ?string
    {
        return $this->apiType;
    }

    /**
     * @inheritDoc
     */
    public function getRestApiPassword(): ?string
    {
        return $this->apiPassword;
    }

    /**
     * @param LoggerInterface $value
     */
    public function setLogger(LoggerInterface $value)
    {
        $this->logger = $value;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        if (!$this->logger) {
            $name = 'AccordIntegration.rest';
            $this->logger = new Logger($name);
            $this->logger->pushHandler(new StreamHandler(BP . '/var/log/' . $name . '.log', Logger::DEBUG));
        }
        return $this->logger;
    }

    /**
     * @return HandlerStack|null
     */
    public function getHandler(): ?HandlerStack
    {
        if (!$this->getLogger()) {
            return null;
        }

        $stack = HandlerStack::create();

        $stack->push(
            Middleware::log(
                $this->getLogger(),
                new MessageFormatter(MessageFormatter::DEBUG)
            )
        );

        return $stack;
    }
}
