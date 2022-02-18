<?php

namespace Accord\Integration\Api\Response;

/**
 * @property-read string $fileName
 * @property-read string $fileStream
 */
class Documents extends Response
{
    /**
     * @return void
     */
    protected function validate()
    {
        if (!$this->fileName) {
            throw $this->error('fileName is not specified');
        }

        if (!$this->fileStream) {
            throw $this->error('fileStream is not specified');
        }
    }

    /**
     * @return null|string
     */
    public function getFileContent()
    {
        if ($this->fileStream) {
            $stream = explode(',', $this->fileStream);
            return base64_decode($stream[1]);
        }
        
        return null;
    }
}
