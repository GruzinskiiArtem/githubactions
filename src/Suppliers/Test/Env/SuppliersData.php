<?php
namespace Accord\Suppliers\Test\Env;

trait SuppliersData
{

    /**
     * @param int $countRows
     * @return array
     */
    public function getValidSuppliers($countRows = 10)
    {
        return $this->parseData("/json/suppliers-valid-{$countRows}.json");
    }

    public function getInvalidSupplierName()
    {
        return $this->parseData("/json/suppliers-invalid-name-10(1-invalid).json");
    }

    public function getInvalidSupplierCode()
    {
        return $this->parseData("/json/suppliers-invalid-code-10(1-invalid).json");
    }

    /**
     * @return array
     */
    protected function parseData ($file)
    {
        $json = file_get_contents(__DIR__ . $file);
        $data = json_decode($json, true);
        return $data;
    }

}