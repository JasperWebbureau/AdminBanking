<?php
declare(strict_types=1);namespace Flexgrid\Modules\AdminBanking\Entity;use Repository\RepositoryEntity;
/** @FG\Entity[name=admin_bank_account,repository=Flexgrid\Modules\AdminBanking\Repository\BankAccountRecordRepository,type=Module,in_menu=false]
 * @FG\Index::tenant_public[columns={tenantId,publicId},unique=true]
 * @FG\Index::tenant_account_reference[columns={tenantId,accountReference},unique=true]
 * @FG\Index::tenant_iban[columns={tenantId,iban},unique=true]
 */
final class BankAccountRecord extends RepositoryEntity{/** @FG\Column[type=primary] */protected$id;/** @FG\Column[type=varchar,length=64,required=true] */protected$tenantId;/** @FG\Column[type=varchar,length=64,required=true] */protected$publicId;/** @FG\Column[type=varchar,length=128,required=true] */protected$name;/** @FG\Column[type=varchar,length=64,required=true] */protected$accountReference;/** @FG\Column[type=varchar,length=34] */protected$iban;/** @FG\Column[type=varchar,length=3,required=true] */protected$currency;/** @FG\Column[type=tinyint,required=true] */protected$isActive;/** @FG\Column[type=bigint,required=true] */protected$createdAt;/** @FG\Column[type=bigint,required=true] */protected$updatedAt;

    // --- Auto-generated getters and setters ---

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param mixed $value
     * @return $this
     */
    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Get the value of tenantId
     */
    public function getTenantId()
    {
        return $this->tenantId;
    }

    /**
     * Set the value of tenantId
     *
     * @param mixed $value
     * @return $this
     */
    public function setTenantId($value)
    {
        $this->tenantId = $value;
        return $this;
    }

    /**
     * Get the value of publicId
     */
    public function getPublicId()
    {
        return $this->publicId;
    }

    /**
     * Set the value of publicId
     *
     * @param mixed $value
     * @return $this
     */
    public function setPublicId($value)
    {
        $this->publicId = $value;
        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param mixed $value
     * @return $this
     */
    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }

    /**
     * Get the value of accountReference
     */
    public function getAccountReference()
    {
        return $this->accountReference;
    }

    /**
     * Set the value of accountReference
     *
     * @param mixed $value
     * @return $this
     */
    public function setAccountReference($value)
    {
        $this->accountReference = $value;
        return $this;
    }

    /**
     * Get the value of iban
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set the value of iban
     *
     * @param mixed $value
     * @return $this
     */
    public function setIban($value)
    {
        $this->iban = $value;
        return $this;
    }

    /**
     * Get the value of currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the value of currency
     *
     * @param mixed $value
     * @return $this
     */
    public function setCurrency($value)
    {
        $this->currency = $value;
        return $this;
    }

    /**
     * Get the value of isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set the value of isActive
     *
     * @param mixed $value
     * @return $this
     */
    public function setIsActive($value)
    {
        $this->isActive = $value;
        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param mixed $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        $this->createdAt = $value;
        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param mixed $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        $this->updatedAt = $value;
        return $this;
    }

    /**
     * Get the value of makeTime
     */
    public function getMakeTime()
    {
        return $this->makeTime;
    }

    /**
     * Set the value of makeTime
     *
     * @param mixed $value
     * @return $this
     */
    public function setMakeTime($value)
    {
        $this->makeTime = $value;
        return $this;
    }

}
