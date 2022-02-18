<?php
namespace Accord\Integration\Api\Response;

class ResponseException extends \RuntimeException
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * ResponseException constructor.
     * @param string $message
     * @param int $code
     * @param ResponseInterface $response
     */
    public function __construct($message = "Invalid response", $code = 0, ResponseInterface $response = null)
    {
        $this->response = $response;
        parent::__construct($message, $code);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}