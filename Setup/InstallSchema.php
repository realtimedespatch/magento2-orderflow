<?php

namespace RealtimeDespatch\OrderFlow\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Create table 'rtd_requests'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('rtd_requests'))
            ->addColumn(
                'request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Request ID'
            )->addColumn(
                'message_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Message ID'
            )->addColumn(
                'scope_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                150,
                ['nullable' => true],
                'Scope ID'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Request Type'
            )
            ->addColumn(
                'entity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Entity Type'
            )
            ->addColumn(
                'operation',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Operation Type'
            )
            ->addColumn(
                'request_body',
                \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                null,
                ['nullable' => true, 'default' => null],
                'Request Body'
            )
            ->addColumn(
                'response_body',
                \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                null,
                ['nullable' => true, 'default' => null],
                'Response Body'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'processed_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Processed At'
            )
            ->addIndex(
                $setup->getIdxName('rtd_request', ['message_id']),
                ['message_id']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request', ['entity']),
                ['entity']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request', ['type']),
                ['type']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request', ['created_at']),
                ['created_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request', ['processed_at']),
                ['processed_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request', ['fulltext']),
                ['message_id', 'operation'],
                ['type' => 'fulltext']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request', ['orderflow']),
                ['type', 'entity', 'processed_at', 'message_id']
            );

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'rtd_request_lines'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('rtd_request_lines'))
            ->addColumn(
                'line_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Request Line Id'
            )
            ->addColumn(
                'request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Request Id'
            )
            ->addColumn(
                'sequence_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => true, 'default' => null],
                'Sequence Id'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Request Type'
            )
            ->addColumn(
                'body',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Request Body'
            )
            ->addColumn(
                'response',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Response Body'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'processed_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Processed At'
            )
            ->addIndex(
                $setup->getIdxName('rtd_request_lines', ['fulltext']),
                ['type'],
                ['type' => 'fulltext']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request_lines', ['request_id']),
                ['request_id']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request_lines', ['created_at']),
                ['created_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request_lines', ['processed_at']),
                ['processed_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request_lines', ['type']),
                ['type']
            )
            ->addIndex(
                $setup->getIdxName('rtd_request_lines', ['sequence_id']),
                ['sequence_id']
            )
            ->addForeignKey(
                $setup->getFkName('rtd_request_lines', 'request_id', 'rtd_requests', 'request_id'),
                'request_id',
                $setup->getTable('rtd_requests'),
                'request_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment(
                'Request Line'
            );

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'rtd_imports'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('rtd_imports'))
            ->addColumn(
                'import_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Import ID'
            )->addColumn(
                'request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['nullable' => false, 'unsigned' => true,],
                'Request ID'
            )->addColumn(
                'message_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Message ID'
            )->addColumn(
                'operation',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Operation'
            )
            ->addColumn(
                'entity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Entity Type'
            )
            ->addColumn(
                'successes',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 0],
                'Successes'
            )
            ->addColumn(
                'duplicates',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 0],
                'Duplicates'
            )
            ->addColumn(
                'superseded',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 0],
                'Superseded'
            )
            ->addColumn(
                'failures',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 0],
                'Failures'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'viewed_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Viewed At'
            )
            ->addIndex(
                $setup->getIdxName('rtd_import_lines', ['notifications']),
                ['failures', 'viewed_at', 'created_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_imports', ['fulltext']),
                ['message_id', 'entity', 'operation'],
                ['type' => 'fulltext']
            )
            ->addIndex(
                $setup->getIdxName('rtd_imports', ['message_id']),
                ['message_id']
            )
            ->addIndex(
                $setup->getIdxName('rtd_imports', ['entity']),
                ['entity']
            )
            ->addIndex(
                $setup->getIdxName('rtd_imports', ['created_at']),
                ['created_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_imports', ['viewed_at']),
                ['failures']
            )
            ->addIndex(
                $setup->getIdxName('rtd_imports', ['viewed_at']),
                ['viewed_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_imports', ['request_id']),
                ['request_id']
            );

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'rtd_import_lines'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('rtd_import_lines'))
            ->addColumn(
                'line_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Import Line Id'
            )
            ->addColumn(
                'import_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Import Id'
            )
            ->addColumn(
                'sequence_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false],
                'Sequence Id'
            )
            ->addColumn(
                'result',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Result Type'
            )
            ->addColumn(
                'reference',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Reference'
            )
            ->addColumn(
                'operation',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Operation'
            )
            ->addColumn(
                'entity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Entity Type'
            )
            ->addColumn(
                'message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Message'
            )
            ->addColumn(
                'additional_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                null,
                ['nullable' => true],
                'Additional Data'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addIndex(
                $setup->getIdxName('rtd_import_lines', ['fulltext']),
                ['reference', 'entity', 'operation', 'result'],
                ['type' => 'fulltext']
            )
            ->addIndex(
                $setup->getIdxName('rtd_import_lines', ['sequence_id']),
                ['sequence_id']
            )
            ->addIndex(
                $setup->getIdxName('rtd_import_lines', ['reference']),
                ['reference']
            )
            ->addIndex(
                $setup->getIdxName('rtd_import_lines', ['operation']),
                ['operation']
            )
            ->addIndex(
                $setup->getIdxName('rtd_import_lines', ['entity']),
                ['entity']
            )
            ->addIndex(
                $setup->getIdxName('rtd_import_lines', ['created_at']),
                ['created_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_import_lines', ['import_id']),
                ['import_id']
            )
            ->addIndex(
                $setup->getIdxName('rtd_import_lines', ['orderflow']),
                ['entity', 'reference', 'sequence_id']
            )
            ->addForeignKey(
                $setup->getFkName('rtd_import_lines', 'import_id', 'rtd_imports', 'import_id'),
                'import_id',
                $setup->getTable('rtd_imports'),
                'import_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment(
                'Import Line'
            );

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'rtd_exports'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('rtd_exports'))
            ->addColumn(
                'export_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Export ID'
            )->addColumn(
                'request_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['nullable' => false, 'unsigned' => true,],
                'Request ID'
            )->addColumn(
                'message_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Message ID'
            )->addColumn(
                'scope_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                150,
                ['nullable' => true],
                'Scope ID'
            )->addColumn(
                'operation',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Operation'
            )
            ->addColumn(
                'entity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Entity Type'
            )
            ->addColumn(
                'successes',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 0],
                'Successes'
            )
            ->addColumn(
                'duplicates',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 0],
                'Duplicates'
            )
            ->addColumn(
                'superseded',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 0],
                'Superseded'
            )
            ->addColumn(
                'failures',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => 0],
                'Failures'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'viewed_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Viewed At'
            )
            ->addIndex(
                $setup->getIdxName('rtd_export_lines', ['notifications']),
                ['failures', 'viewed_at', 'created_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_exports', ['fulltext']),
                ['message_id', 'entity', 'operation'],
                ['type' => 'fulltext']
            )
            ->addIndex(
                $setup->getIdxName('rtd_exports', ['message_id']),
                ['message_id']
            )
            ->addIndex(
                $setup->getIdxName('rtd_exports', ['entity']),
                ['entity']
            )
            ->addIndex(
                $setup->getIdxName('rtd_exports', ['created_at']),
                ['created_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_imports', ['viewed_at']),
                ['failures']
            )
            ->addIndex(
                $setup->getIdxName('rtd_imports', ['viewed_at']),
                ['viewed_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_exports', ['request_id']),
                ['request_id']
            );

        $setup->getConnection()->createTable($table);

        /**
         * Create table 'rtd_export_lines'
         */
        $table = $setup->getConnection()->newTable($setup->getTable('rtd_export_lines'))
            ->addColumn(
                'line_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Export Line Id'
            )
            ->addColumn(
                'export_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Export Id'
            )
            ->addColumn(
                'result',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Result Type'
            )
            ->addColumn(
                'reference',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Reference'
            )
            ->addColumn(
                'operation',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Operation'
            )
            ->addColumn(
                'entity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false, 'length' => 150],
                'Entity Type'
            )
            ->addColumn(
                'message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Message'
            )
            ->addColumn(
                'detail',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Detail'
            )
            ->addColumn(
                'data',
                \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                null,
                ['nullable' => true],
                'Line Data'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addIndex(
                $setup->getIdxName('rtd_export_lines', ['fulltext']),
                ['reference', 'entity', 'operation', 'result'],
                ['type' => 'fulltext']
            )
            ->addIndex(
                $setup->getIdxName('rtd_export_lines', ['reference']),
                ['reference']
            )
            ->addIndex(
                $setup->getIdxName('rtd_export_lines', ['operation']),
                ['operation']
            )
            ->addIndex(
                $setup->getIdxName('rtd_export_lines', ['entity']),
                ['entity']
            )
            ->addIndex(
                $setup->getIdxName('rtd_export_lines', ['created_at']),
                ['created_at']
            )
            ->addIndex(
                $setup->getIdxName('rtd_export_lines', ['export_id']),
                ['export_id']
            )
            ->addForeignKey(
                $setup->getFkName('rtd_export_lines', 'export_id', 'rtd_exports', 'export_id'),
                'export_id',
                $setup->getTable('rtd_exports'),
                'export_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment(
                'Export Line'
            );

        $setup->getConnection()->createTable($table);

        // Order Grid Export Status Column
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'orderflow_export_status',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'comment' => 'OrderFlow Export Status'
            ]
        );

        $setup->endSetup();
    }
}