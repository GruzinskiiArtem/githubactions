<?php
namespace Accord\Integration\Api\Client;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ClientException extends \GuzzleHttp\Exception\RequestException
{

    public function __construct($message, RequestInterface $request, ResponseInterface $response = null, \Exception $previous = null, array $handlerContext = [])
    {
        if ($response) {
            $message = $this->generateMessage($message, $response);
        }
        parent::__construct($message, $request, $response, $previous, $handlerContext);
    }

    protected function generateMessage($message, ResponseInterface $response)
    {
        $body = (string)$response->getBody();
        if ($body) {
            $object = json_decode($body, true);
            if (isset($object['errors'][0]['errorMsg'])) {
                $message = $object['errors'][0]['errorMsg'];
            }
        }
        return $message;
    }

}