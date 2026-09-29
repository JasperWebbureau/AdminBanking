<?php
declare(strict_types=1);namespace Flexgrid\Modules\AdminBanking\Entity;use Repository\RepositoryEntity;
/** @FG\Entity[name=admin_bank_match_proposal,repository=Flexgrid\Modules\AdminBanking\Repository\BankMatchProposalRecordRepository,type=Module,in_menu=false]
 * @FG\Index::tenant_public[columns={tenantId,publicId},unique=true]
 * @FG\Index::tenant_transaction_target[columns={tenantId,transactionPublicId,targetType,targetPublicId},unique=true]
 * @FG\Index::tenant_transaction_confidence[columns={tenantId,transactionPublicId,confidenceBasisPoints}]
 */
final class BankMatchProposalRecord extends RepositoryEntity{/** @FG\Column[type=primary] */protected$id;/** @FG\Column[type=varchar,length=64,required=true] */protected$tenantId;/** @FG\Column[type=varchar,length=64,required=true] */protected$publicId;/** @FG\Column[type=varchar,length=64,required=true] */protected$transactionPublicId;/** @FG\Column[type=varchar,length=32,required=true] */protected$targetType;/** @FG\Column[type=varchar,length=64,required=true] */protected$targetPublicId;/** @FG\Column[type=int,required=true] */protected$confidenceBasisPoints;/** @FG\Column[type=varchar,length=500,required=true] */protected$reason;/** @FG\Column[type=bigint,required=true] */protected$createdAt;

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
     * Get the value of transactionPublicId
     */
    public function getTransactionPublicId()
    {
        return $this->transactionPublicId;
    }

    /**
     * Set the value of transactionPublicId
     *
     * @param mixed $value
     * @return $this
     */
    public function setTransactionPublicId($value)
    {
        $this->transactionPublicId = $value;
        return $this;
    }

    /**
     * Get the value of targetType
     */
    public function getTargetType()
    {
        return $this->targetType;
    }

    /**
     * Set the value of targetType
     *
     * @param mixed $value
     * @return $this
     */
    public function setTargetType($value)
    {
        $this->targetType = $value;
        return $this;
    }

    /**
     * Get the value of targetPublicId
     */
    public function getTargetPublicId()
    {
        return $this->targetPublicId;
    }

    /**
     * Set the value of targetPublicId
     *
     * @param mixed $value
     * @return $this
     */
    public function setTargetPublicId($value)
    {
        $this->targetPublicId = $value;
        return $this;
    }

    /**
     * Get the value of confidenceBasisPoints
     */
    public function getConfidenceBasisPoints()
    {
        return $this->confidenceBasisPoints;
    }

    /**
     * Set the value of confidenceBasisPoints
     *
     * @param mixed $value
     * @return $this
     */
    public function setConfidenceBasisPoints($value)
    {
        $this->confidenceBasisPoints = $value;
        return $this;
    }

    /**
     * Get the value of reason
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set the value of reason
     *
     * @param mixed $value
     * @return $this
     */
    public function setReason($value)
    {
        $this->reason = $value;
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
