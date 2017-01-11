<?php

namespace RealtimeDespatch\OrderFlow\Api\Data;

interface ExportLineInterface
{
    /* Params */
    const LINE_ID         = 'line_id';
    const IMPORT_ID       = 'export_id';
    const RESULT          = 'result';
    const REFERENCE       = 'reference';
    const OPERATION       = 'operation';
    const ENTITY          = 'entity';
    const MESSAGE         = 'message';
    const DETAIL          = 'detail';
    const ADDITIONAL_DATA = 'additional_data';
    const CREATED_AT      = 'created_at';
    const PROCESSED_AT    = 'processed_at';

    /* Result Types */
    const RESULT_SUCCESS    = 'Success';
    const RESULT_DUPLICATE  = 'Duplicate';
    const RESULT_FAILURE    = 'Failure';
    const RESULT_SUPERSEDED = 'Superseded';

    /* Entity Export Status Types */
    const ENTITY_STATUS_EXPORTED = 'Exported';
    const ENTITY_STATUS_QUEUED = 'Queued';
    const ENTITY_STATUS_FAILED = 'Failed';
    const ENTITY_STATUS_CANCELLED = 'Cancelled';

    /* Operation Types */
    const OP_EXPORT = 'Export';
    const OP_CREATE = 'Create';
    const OP_CANCEL = 'Cancel';
    const OP_UPDATE = 'Update';

    /**
     * Get ID
     *
     * @return integer|null
     */
    public function getId();

    /**
     * Get Export ID
     *
     * @return integer|null
     */
    public function getExportId();

    /**
     * Get Result
     *
     * @return string|null
     */
    public function getResult();

    /**
     * Get Reference
     *
     * @return string|null
     */
    public function getReference();

    /**
     * Get Operation
     *
     * @return string|null
     */
    public function getOperation();

    /**
     * Get Entity
     *
     * @return string|null
     */
    public function getEntity();

    /**
     * Get Message
     *
     * @return string|null
     */
    public function getMessage();

    /**
     * Get Detail
     *
     * @return string|null
     */
    public function getDetail();

    /**
     * Get Additional Data
     *
     * @return string|null
     */
    public function getAdditionalData();

    /**
     * Get Created Timestamp
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get Processed Timestamp
     *
     * @return string|null
     */
    public function getProcessedAt();

    /**
     * Set Export Id
     *
     * @param integer $exportId
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setExportId($exportId);

    /**
     * Set Result
     *
     * @param string $result
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setResult($result);

    /**
     * Set Reference
     *
     * @param string $reference
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setReference($reference);

    /**
     * Set Operation
     *
     * @param string $operation
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setOperation($operation);

    /**
     * Set Entity
     *
     * @param string $entity
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setEntity($entity);

    /**
     * Set Message
     *
     * @param string $message
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setMessage($message);

    /**
     * Set Detail
     *
     * @param string $detail
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setDetail($detail);

    /**
     * Set Additional Data
     *
     * @param string $additionalData
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setAdditionalData($additionalData);

    /**
     * Set Created Timestamp
     *
     * @param string $created
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setCreatedAt($created);

    /**
     * Set Processed Timestamp
     *
     * @param string $processed
     *
     * @return \RealtimeDespatch\OrderFlow\Api\Data\ExportLineInterface
     */
    public function setProcessedAt($processed);

    /**
     * Checks whether this export line was successful
     *
     * @return boolean
     */
    public function isSuccess();

    /**
     * Checks whether this export line was a failure
     *
     * @return boolean
     */
    public function isFailure();

    /**
     * Returns the export status for the related entity.
     *
     * @return mixed
     */
    public function getEntityExportStatus();
}