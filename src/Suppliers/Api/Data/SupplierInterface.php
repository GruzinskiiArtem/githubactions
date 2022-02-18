<?php
namespace Accord\Suppliers\Api\Data;

interface SupplierInterface
{
    const ID            = 'entity_id';
    const CODE          = 'code';
    const NAME          = 'name';
    const CREATED_AT    = 'created_at';
    const UPDATED_AT    = 'updated_at';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @param $name
     * @return $this
     */
    public function setName($name);
}
