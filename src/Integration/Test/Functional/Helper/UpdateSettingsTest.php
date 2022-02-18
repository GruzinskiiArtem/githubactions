<?php
namespace Accord\Integration\Test\Functional\Helper;

use Accord\Integration\Test\Env\ApiHelpers;
use Accord\Integration\Test\Env\SettingsExample;
use Accord\Integration\Helper\UpdateSettings as Api;

class UpdateSettingsTest extends \PHPUnit_Framework_TestCase
{
    use ApiHelpers;
    use SettingsExample;

    /**
     * @provider
     * @return array[[\Accord\Integration\Helper\Api]]
     */
    public function providerGetHelpers()
    {
        return [
            [$this->getPhalcon_Api(Api::class)],
            //[$this->getSystem_Api()],
            //[$this->getAccord_Api()],
        ];
    }


    /**
     * @test
     * @dataProvider providerGetHelpers
     * @param Api $apiHelper
     */
    public function testUpdateSettings(Api $apiHelper)
    {
        $updateSettings = $apiHelper->updateSettings($this->getSettings1());
        $this->assertEquals($updateSettings->getStatusCode(), 200);

        $updateSettings = $apiHelper->updateSettings($this->getSettings2());
        $this->assertEquals($updateSettings->getStatusCode(), 200);
    }


}