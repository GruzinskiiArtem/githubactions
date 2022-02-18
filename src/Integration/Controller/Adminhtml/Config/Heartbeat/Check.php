<?php
namespace Accord\Integration\Controller\Adminhtml\Config\Heartbeat;

use Accord\Integration\Api\Client\ClientException;
use Accord\Integration\Controller\Adminhtml\Config\Heartbeat;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\App\Action;
use Accord\Integration\Helper\HeartbeatCheck;

class Check extends Heartbeat
{

    /**
     * @var HeartbeatCheck
     */
    protected $helperHeartBeatCheck;

    public function __construct(Action\Context $context, HeartbeatCheck $helperHeartBeatCheck)
    {
        $this->helperHeartBeatCheck = $helperHeartBeatCheck;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = ['status' => 'ok', 'message' => __('Connection with Accord Established')];
        $restParameters = $this->parseRequestParams($this->getRequest());
        try {
            if (empty($restParameters)) {
                throw  new \Exception('Bad parameters');
            }

            $this->helperHeartBeatCheck->check($restParameters);
        } catch (ClientException $e) {
            $message = __('Unable to Establish a Connection with Accord');
            if ($e->getCode() == 401) {
                $message = $message . ' - ' . __('Unauthorized Username and/or Password');
            }
            $result = [
                'status' => 'error',
                'message' => $message,
            ];
        } catch (\Exception $e) {
            $result = ['status' => 'error', 'message' => $e->getMessage()];
        }

        $this->sendJsonResponse($result);
    }

    protected function parseRequestParams(RequestInterface $request)
    {
        $result['apiType'] = $request->getParam('groups', null)['rest_parameters']['fields']['apiType']['value'];
        $result['apiEndpoint'] = $request->getParam('groups', null)['rest_parameters']['fields']['apiEndpoint']['value'];
        $result['apiUsername'] = $request->getParam('groups', null)['rest_parameters']['fields']['apiUsername']['value'];
        $result['apiPassword'] = $request->getParam('groups', null)['rest_parameters']['fields']['apiPassword']['value'];
        return $result;
    }

    /**
     * @param array $data
     */
    protected function sendJsonResponse(array $data)
    {
        /** @var \Magento\Framework\App\Response\Http\Interceptor $response */
        $response = $this->getResponse();
        $response->representJson(json_encode($data));
        $response->send();
    }
}
