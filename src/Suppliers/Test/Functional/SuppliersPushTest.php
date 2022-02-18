<?php
namespace Accord\Suppliers\Test\Functional;

use Accord\Api\Test\Functional\ApiTest;
use Accord\Suppliers\Model\Supplier;
use Accord\Suppliers\Test\Env\SuppliersData;

class SuppliersPushTest extends ApiTest
{
    use SuppliersData;

    /**
     * @test
     */
    public function testPushValidData()
    {
        $validData = $this->getValidSuppliers(10);
        $validDataCount  = count($validData);

        $response = $this->post('accord/suppliers', [
            'headers' => $this->getHeaders(),
            'json' => $validData,
        ]);

        $body = (string)$response->getBody();
        $this->assertEquals($body, '"success"');

        $actualCount = $this->getSupplierModel()->getCollection()->getSize();
        $this->assertEquals($validDataCount, $actualCount);

        return $actualCount;
    }

    /**
     * @depends testPushValidData
     */
    public function testPushAndOverwriteData()
    {
        $validData = $this->getValidSuppliers(9);
        $validDataCount  = count($validData);

        $response = $this->post('accord/suppliers', [
            'headers' => $this->getHeaders(),
            'json' => $validData,
        ]);

        $body = (string)$response->getBody();
        $this->assertEquals($body, '"success"');

        $actualCount = $this->getSupplierModel()->getCollection()->getSize();
        $this->assertEquals($validDataCount, $actualCount);
    }


    /**
     * @expectedException \GuzzleHttp\Exception\ClientException
     */
    public function testPushEmptyData()
    {
        $response = $this->post('accord/suppliers', [
            'headers' => $this->getHeaders(),
            'json' => [],
        ]);
    }

    public function testBadCodeData()
    {
        $this->truncateSuppliers();
        $data = $this->getInvalidSupplierCode();

        try {
            $response = $this->post('accord/suppliers', [
                'headers' => $this->getHeaders(),
                'json' => $data,
            ]);
        } catch (\Exception $e) {}

        $actualCount = $this->getSupplierModel()->getCollection()->getSize();
        $this->assertEquals(count($data)-1, $actualCount);
    }

    public function testBadNameData()
    {
        $this->truncateSuppliers();
        $data = $this->getInvalidSupplierName();

        try {
            $response = $this->post('accord/suppliers', [
                'headers' => $this->getHeaders(),
                'json' => $data,
            ]);
        } catch (\Exception $e) {}

        $actualCount = $this->getSupplierModel()->getCollection()->getSize();
        $this->assertEquals(count($data)-1, $actualCount);
    }


    /**
     * @return Supplier
     */
    protected function getSupplierModel()
    {
        return $this->getObjectManager()->create('Accord\Suppliers\Model\Supplier');
    }

    protected function truncateSuppliers()
    {
        /** @var \Accord\Suppliers\Model\ResourceModel\Supplier\Collection $suppliers */
        $suppliers = $this->getObjectManager()->create('Accord\Suppliers\Model\Supplier')->getCollection();

        /** @var Supplier $supplier */
        foreach ($suppliers as $supplier) {
            $supplier->delete();
        }
    }

}