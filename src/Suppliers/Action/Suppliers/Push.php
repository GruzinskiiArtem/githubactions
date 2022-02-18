<?php
namespace Accord\Suppliers\Action\Suppliers;

use Accord\Api\Action\RestAction;
use Accord\Suppliers\Model\ResourceModel\Supplier\Collection as SupplierCollection;
use Accord\Suppliers\Model\Supplier;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\ObjectManager;

class Push extends RestAction
{

    /**
     * @api
     * @return string|array
     */
    public function execute()
    {
        set_time_limit(0);
        $errors = [];

        $reqBody = $this->getBodyParams();

        try {
            $this->validateBody($reqBody);
        } catch (\Exception $e) {
            $errors[] = [
                'errorMsg' => $e->getMessage(),
            ];
        }

        $affectedRowCodes = [];

        if (empty($errors)) {
            foreach ($reqBody as $data) {
                try {
                    $this->saveSupplier($data);
                    $affectedRowCodes[] = $data['supplierCode'];
                } catch (\Exception $e) {
                    $errors[] = [
                        'supplierCode' => isset($data['supplierCode']) ? $data['supplierCode'] : '',
                        'supplierName' => isset($data['supplierName']) ? $data['supplierName'] : '',
                        'errorMsg' => $e->getMessage(),
                    ];
                }
            }
        }


        $result = "success";
        if (count($errors)) {
            $this->setStatusCode(422);
            $result = $errors;
        } else {
            $this->clearUnUseSuppliers($affectedRowCodes);
        }

        $this->clearCache();

        return $result;
    }

    protected function validateBody($reqBody)
    {
        if (empty($reqBody)) {
            throw new \Exception('Body is empty');
        }

        if (!is_array($reqBody)) {
            throw new \Exception('Body is not array');
        }
    }

    /**
     * @param $data
     * @return Supplier
     */
    protected function saveSupplier($data)
    {
        /** @var Supplier $supplier */
        $supplier = $this->getSupplierModel()->load($data['supplierCode'], Supplier::CODE);
        $supplier->setCode($data['supplierCode']);
        $supplier->setName($data['supplierName']);
        $supplier->setData(Supplier::UPDATED_AT, date('Y-m-d H:i:s'));
        $supplier->save();

        return $supplier;
    }

    protected function clearUnUseSuppliers(array $codes)
    {
        if (empty($codes)) {
            return;
        }

        /** @var SupplierCollection $suppliers */
        $suppliers = $this->getSupplierModel()->getCollection();
        $suppliers->addFieldToFilter(Supplier::CODE, ['nin' => $codes]);
        $suppliers->load();
        /** @var Supplier $supplier */
        foreach ($suppliers as $supplier) {
            $supplier->delete();
        }
    }

    /**
     * @return Supplier
     */
    protected function getSupplierModel()
    {
        return ObjectManager::getInstance()->create('Accord\Suppliers\Model\Supplier');
    }

}
