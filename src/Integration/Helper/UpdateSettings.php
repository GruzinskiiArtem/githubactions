<?php

declare(strict_types=1);

namespace Accord\Integration\Helper;

use Accord\Integration\Api\Commands\UpdateSettings as UpdateSettingsCommand;
use Accord\Integration\Api\Request\UpdateSettings as UpdateSettingsRequest;
use Accord\Integration\Api\Response\EmptyResponse;
use Accord\Integration\Api\Response\ResponseInterface;

class UpdateSettings extends \Accord\Integration\Helper\Api
{
    /**
     * @param array $data
     *
     * @return ResponseInterface
     */
    public function updateSettings(array $data): ResponseInterface
    {
        $request = $this->objectManager->get(UpdateSettingsRequest::class);
        $response = $this->objectManager->get(EmptyResponse::class);

        return (new UpdateSettingsCommand($this->client, $request, $response))
            ->execute($data);
    }
}
