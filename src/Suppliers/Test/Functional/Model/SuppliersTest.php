<?php
namespace Accord\Suppliers\Test\Functional\Model;

use Accord\Integration\Test\Env\ObjectManager as AccordTestObjectManager;
use Accord\Suppliers\Api\Data\SupplierInterface;
use Accord\Suppliers\Model\Supplier;
use Accord\Suppliers\Test\Env\SuppliersData;
use PHPUnit_Framework_TestCase;

class SuppliersTest extends PHPUnit_Framework_TestCase
{
    use AccordTestObjectManager;
    use SuppliersData;

    protected function truncateSuppliers()
    {
        /** @var \Accord\Suppliers\Model\ResourceModel\Supplier\Collection $suppliers */
        $suppliers = $this->getObjectManager()->create('Accord\Suppliers\Model\Supplier')->getCollection();

        /** @var Supplier $supplier */
        foreach ($suppliers as $supplier) {
            $supplier->delete();
        }
    }

    /**
     * @param $data
     * @return Supplier
     */
    protected function initValidSupplier ($data)
    {
        $this->truncateSuppliers();

        foreach ($data as $jsonSupplier) {
            /** @var Supplier $supplier */
            $supplier = $this->getSupplierModel();
            $supplier->setCode($jsonSupplier['supplierCode']);
            $supplier->setName($jsonSupplier['supplierName']);
            $supplier->save();
        }
    }

    /**
     * @return Supplier
     */
    protected function getSupplierModel()
    {
        return $this->getObjectManager()->create('Accord\Suppliers\Model\Supplier');
    }

    public function testModel()
    {
        $jsonSuppliers = $this->getValidSuppliers();
        $this->initValidSupplier([$jsonSuppliers[0]]);
        /** @var Supplier $supplier */
        $supplier = $this->getSupplierModel()->load($jsonSuppliers[0]['supplierCode'], SupplierInterface::CODE);

        $this->assertNotEmpty($supplier, 'Model is not initialized');
        $this->assertEquals($jsonSuppliers[0]['supplierCode'], $supplier->getCode(), 'Code is not true');
        $this->assertEquals($jsonSuppliers[0]['supplierName'], $supplier->getName(), 'Name is not true');
        $this->assertNotEmpty($supplier->getCreatedAt(), 'CreatedAt is not initialized');
        $this->assertNotEmpty($supplier->getUpdatedAt(), 'Updated is not initialized');
    }


    protected function tearDown()
    {
        $this->truncateSuppliers();
    }

}
