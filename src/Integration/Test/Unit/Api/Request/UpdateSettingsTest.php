<?php
namespace Accord\Integration\Api\Request;

use Accord\Integration\Test\Env\SettingsExample;

class UpdateSettingsTest extends \PHPUnit_Framework_TestCase
{
    use SettingsExample;

    /**
     * @provider
     * @return array
     */
    public function providerGetData()
    {
        return [
            [$this->getSettings1()],
            [$this->getSettings2()],
        ];
    }

    /**
     * @test
     * @param $data
     * @dataProvider providerGetData
     * @covers       \Accord\Integration\Api\Request\UpdateSettings
     */
    public function testGetFormData($data)
    {
        $params = new UpdateSettings($data);
        $newData = $params->getData();
        foreach ($newData as $item) {
            $this->assertArrayHasKey('setting', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('category', $item);
            $this->assertArrayHasKey('value', $item);
        }
    }

    /**
     * @test
     * @covers  \Accord\Integration\Api\Request\UpdateSettings::convert
     * @covers  \Accord\Integration\Api\Request\UpdateSettings::error
     * @expectedException \Accord\Integration\Api\Request\RequestException
     * @expectedExceptionMessage Data is empty
     */
    public function testConvertFail()
    {
        $data = [];
        $params = new UpdateSettings();
        $params->setData($data);
    }


    /**
     * @test
     * @covers       \Accord\Integration\Api\Request\UpdateSettings::getSetting
     * @dataProvider providerGetData
     * @expectedException \Accord\Integration\Api\Request\RequestException
     * @expectedExceptionMessage Invalid param item
     */
    public function testGetSettingFail($data)
    {
        foreach ($data as &$item) {
            unset($item['setting']);
        }
        $params = new UpdateSettings();
        $params->setData($data);
        $st = 1;
    }

    /**
     * @test
     * @covers       \Accord\Integration\Api\Request\UpdateSettings::getLabel
     * @dataProvider providerGetData
     * @expectedException \Accord\Integration\Api\Request\RequestException
     */
    public function testGetLabelFail($data)
    {
        foreach ($data as &$item) {
            unset($item['label']);
        }
        $params = new UpdateSettings();
        $params->setData($data);
    }

    /**
     * @test
     * @covers       \Accord\Integration\Api\Request\UpdateSettings::getCategory
     * @dataProvider providerGetData
     * @expectedException \Accord\Integration\Api\Request\RequestException
     */
    public function testGetCategoryFail($data)
    {
        foreach ($data as &$item) {
            unset($item['category']);
        }
        $params = new UpdateSettings();
        $params->setData($data);
    }
}