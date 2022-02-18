<?php
namespace Accord\Integration\Api\Request;

class RequestException extends \RuntimeException
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * ParserException constructor.
     * @param string $message
     * @param int $code
     * @param RequestInterface $request
     */
    public function __construct($message = "Invalid request", $code = 0, RequestInterface $request = null)
    {
        $this->request = $request;
        parent::__construct($message, $code);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}