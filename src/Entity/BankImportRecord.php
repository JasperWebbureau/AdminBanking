<?php
declare(strict_types=1);namespace Flexgrid\Modules\AdminBanking\Entity;use Repository\RepositoryEntity;
/** @FG\Entity[name=admin_bank_import,repository=Flexgrid\Modules\AdminBanking\Repository\BankImportRecordRepository,type=Module,in_menu=false]
 * @FG\Index::tenant_public[columns={tenantId,publicId},unique=true]
 * @FG\Index::tenant_account_format_checksum[columns={tenantId,bankAccountPublicId,format,checksumSha256},unique=true]
 * @FG\Index::tenant_imported_at[columns={tenantId,importedAt}]
 */
final class BankImportRecord extends RepositoryEntity{/** @FG\Column[type=primary] */protected$id;/** @FG\Column[type=varchar,length=64,required=true] */protected$tenantId;/** @FG\Column[type=varchar,length=64,required=true] */protected$publicId;/** @FG\Column[type=varchar,length=64,required=true] */protected$bankAccountPublicId;/** @FG\Column[type=varchar,length=32,required=true] */protected$format;/** @FG\Column[type=varchar,length=255] */protected$fileName;/** @FG\Column[type=varchar,length=64,required=true] */protected$checksumSha256;/** @FG\Column[type=int,required=true] */protected$rowCount;/** @FG\Column[type=int,required=true] */protected$importedCount;/** @FG\Column[type=int,required=true] */protected$skippedCount;/** @FG\Column[type=bigint,required=true] */protected$importedAt;

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
     * Get the value of format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the value of format
     *
     * @param mixed $value
     * @return $this
     */
    public function setFormat($value)
    {
        $this->format = $value;
        return $this;
    }

    /**
     * Get the value of fileName
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set the value of fileName
     *
     * @param mixed $value
     * @return $this
     */
    public function setFileName($value)
    {
        $this->fileName = $value;
        return $this;
    }

    /**
     * Get the value of checksumSha256
     */
    public function getChecksumSha256()
    {
        return $this->checksumSha256;
    }

    /**
     * Set the value of checksumSha256
     *
     * @param mixed $value
     * @return $this
     */
    public function setChecksumSha256($value)
    {
        $this->checksumSha256 = $value;
        return $this;
    }

    /**
     * Get the value of rowCount
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * Set the value of rowCount
     *
     * @param mixed $value
     * @return $this
     */
    public function setRowCount($value)
    {
        $this->rowCount = $value;
        return $this;
    }

    /**
     * Get the value of importedCount
     */
    public function getImportedCount()
    {
        return $this->importedCount;
    }

    /**
     * Set the value of importedCount
     *
     * @param mixed $value
     * @return $this
     */
    public function setImportedCount($value)
    {
        $this->importedCount = $value;
        return $this;
    }

    /**
     * Get the value of skippedCount
     */
    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    /**
     * Set the value of skippedCount
     *
     * @param mixed $value
     * @return $this
     */
    public function setSkippedCount($value)
    {
        $this->skippedCount = $value;
        return $this;
    }

    /**
     * Get the value of importedAt
     */
    public function getImportedAt()
    {
        return $this->importedAt;
    }

    /**
     * Set the value of importedAt
     *
     * @param mixed $value
     * @return $this
     */
    public function setImportedAt($value)
    {
        $this->importedAt = $value;
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
