<?php
declare(strict_types=1);namespace Flexgrid\Modules\AdminBanking\Entity;use Repository\RepositoryEntity;
/** @FG\Entity[name=admin_bank_transaction,repository=Flexgrid\Modules\AdminBanking\Repository\BankTransactionRecordRepository,type=Module,in_menu=false]
 * @FG\Index::tenant_public[columns={tenantId,publicId},unique=true]
 * @FG\Index::tenant_account_external[columns={tenantId,bankAccountPublicId,externalId},unique=true]
 * @FG\Index::tenant_status_date[columns={tenantId,status,bookedOn}]
 * @FG\Index::tenant_account_date[columns={tenantId,bankAccountPublicId,bookedOn}]
 * @FG\Index::tenant_match_target[columns={tenantId,matchedTargetType,matchedTargetPublicId}]
 */
final class BankTransactionRecord extends RepositoryEntity{/** @FG\Column[type=primary] */protected$id;/** @FG\Column[type=varchar,length=64,required=true] */protected$tenantId;/** @FG\Column[type=varchar,length=64,required=true] */protected$publicId;/** @FG\Column[type=varchar,length=64,required=true] */protected$bankAccountPublicId;/** @FG\Column[type=varchar,length=64,required=true] */protected$bankImportPublicId;/** @FG\Column[type=varchar,length=128,required=true] */protected$externalId;/** @FG\Column[type=varchar,length=10,required=true] */protected$bookedOn;/** @FG\Column[type=bigint,required=true] */protected$amountMinor;/** @FG\Column[type=varchar,length=3,required=true] */protected$currency;/** @FG\Column[type=text,required=true] */protected$counterpartySnapshot;/** @FG\Column[type=text,required=true] */protected$description;/** @FG\Column[type=varchar,length=255] */protected$reference;/** @FG\Column[type=text,required=true] */protected$sourceSnapshot;/** @FG\Column[type=varchar,length=20,required=true] */protected$status;/** @FG\Column[type=varchar,length=32] */protected$matchedTargetType;/** @FG\Column[type=varchar,length=64] */protected$matchedTargetPublicId;/** @FG\Column[type=bigint] */protected$matchedAt;/** @FG\Column[type=bigint,required=true] */protected$createdAt;/** @FG\Column[type=bigint,required=true] */protected$updatedAt;

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
     * Get the value of bankAccountPublicId
     */
    public function getBankAccountPublicId()
    {
        return $this->bankAccountPublicId;
    }

    /**
     * Set the value of bankAccountPublicId
     *
     * @param mixed $value
     * @return $this
     */
    public function setBankAccountPublicId($value)
    {
        $this->bankAccountPublicId = $value;
        return $this;
    }

    /**
     * Get the value of bankImportPublicId
     */
    public function getBankImportPublicId()
    {
        return $this->bankImportPublicId;
    }

    /**
     * Set the value of bankImportPublicId
     *
     * @param mixed $value
     * @return $this
     */
    public function setBankImportPublicId($value)
    {
        $this->bankImportPublicId = $value;
        return $this;
    }

    /**
     * Get the value of externalId
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Set the value of externalId
     *
     * @param mixed $value
     * @return $this
     */
    public function setExternalId($value)
    {
        $this->externalId = $value;
        return $this;
    }

    /**
     * Get the value of bookedOn
     */
    public function getBookedOn()
    {
        return $this->bookedOn;
    }

    /**
     * Set the value of bookedOn
     *
     * @param mixed $value
     * @return $this
     */
    public function setBookedOn($value)
    {
        $this->bookedOn = $value;
        return $this;
    }

    /**
     * Get the value of amountMinor
     */
    public function getAmountMinor()
    {
        return $this->amountMinor;
    }

    /**
     * Set the value of amountMinor
     *
     * @param mixed $value
     * @return $this
     */
    public function setAmountMinor($value)
    {
        $this->amountMinor = $value;
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
     * Get the value of counterpartySnapshot
     */
    public function getCounterpartySnapshot()
    {
        return $this->counterpartySnapshot;
    }

    /**
     * Set the value of counterpartySnapshot
     *
     * @param mixed $value
     * @return $this
     */
    public function setCounterpartySnapshot($value)
    {
        $this->counterpartySnapshot = $value;
        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param mixed $value
     * @return $this
     */
    public function setDescription($value)
    {
        $this->description = $value;
        return $this;
    }

    /**
     * Get the value of reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set the value of reference
     *
     * @param mixed $value
     * @return $this
     */
    public function setReference($value)
    {
        $this->reference = $value;
        return $this;
    }

    /**
     * Get the value of sourceSnapshot
     */
    public function getSourceSnapshot()
    {
        return $this->sourceSnapshot;
    }

    /**
     * Set the value of sourceSnapshot
     *
     * @param mixed $value
     * @return $this
     */
    public function setSourceSnapshot($value)
    {
        $this->sourceSnapshot = $value;
        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param mixed $value
     * @return $this
     */
    public function setStatus($value)
    {
        $this->status = $value;
        return $this;
    }

    /**
     * Get the value of matchedTargetType
     */
    public function getMatchedTargetType()
    {
        return $this->matchedTargetType;
    }

    /**
     * Set the value of matchedTargetType
     *
     * @param mixed $value
     * @return $this
     */
    public function setMatchedTargetType($value)
    {
        $this->matchedTargetType = $value;
        return $this;
    }

    /**
     * Get the value of matchedTargetPublicId
     */
    public function getMatchedTargetPublicId()
    {
        return $this->matchedTargetPublicId;
    }

    /**
     * Set the value of matchedTargetPublicId
     *
     * @param mixed $value
     * @return $this
     */
    public function setMatchedTargetPublicId($value)
    {
        $this->matchedTargetPublicId = $value;
        return $this;
    }

    /**
     * Get the value of matchedAt
     */
    public function getMatchedAt()
    {
        return $this->matchedAt;
    }

    /**
     * Set the value of matchedAt
     *
     * @param mixed $value
     * @return $this
     */
    public function setMatchedAt($value)
    {
        $this->matchedAt = $value;
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
