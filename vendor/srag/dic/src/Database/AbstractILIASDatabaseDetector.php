<?php

namespace srag\DIC\UdfEditor\Database;

use ilDBConstants;
use ilDBPdoInterface;

/**
 * Class AbstractILIASDatabaseDetector
 *
 * @package srag\DIC\UdfEditor\Database
 */
abstract class AbstractILIASDatabaseDetector implements DatabaseInterface
{
    protected ilDBPdoInterface $db;


    /**
     * AbstractILIASDatabaseDetector constructor
     *
     * @param ilDBPdoInterface $db
     */
    public function __construct(ilDBPdoInterface $db)
    {
        $this->db = $db;
    }


    /**
     * @inheritDoc
     */
    static function getReservedWords(): array
    {
        // TODO
        return [];
    }


    /**
     * @inheritDoc
     */
    public static function isReservedWord($a_word): bool
    {
        // TODO
        return false;
    }


    /**
     * @inheritDoc
     */
    public function addFulltextIndex($table_name, $afields, $a_name = 'in'): bool
    {
        return $this->db->addFulltextIndex($a_name, $afields, $a_name);
    }


    /**
     * @inheritDoc
     */
    public function addIndex($table_name, $fields, $index_name = '', $fulltext = false): bool
    {
        return $this->db->addIndex($table_name, $fields, $index_name, $fulltext);
    }


    /**
     * @inheritDoc
     */
    public function addPrimaryKey($table_name, $primary_keys): bool
    {
        $this->db->addPrimaryKey($table_name, $primary_keys);
    }


    /**
     * @inheritDoc
     */
    public function addTableColumn($table_name, $column_name, $attributes): bool
    {
        $this->db->addTableColumn($table_name, $column_name, $attributes);
    }


    /**
     * @inheritDoc
     */
    public function addUniqueConstraint($table, $fields, $name = "con"): bool
    {
        return $this->db->addUniqueConstraint($table, $fields, $name);
    }


    /**
     * @inheritDoc
     */
    public function autoExecute($tablename, $fields, $mode = ilDBConstants::AUTOQUERY_INSERT, $where = false)
    {
        return $this->db->autoExecute($tablename, $fields, $mode, $where);
    }


    /**
     * @inheritDoc
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }


    /**
     * @inheritDoc
     */
    public function buildAtomQuery(): \ilAtomQuery
    {
        return $this->db->buildAtomQuery();
    }


    /**
     * @inheritDoc
     */
    public function cast($a_field_name, $a_dest_type): string
    {
        return $this->db->cast($a_field_name, $a_dest_type);
    }


    /**
     * @inheritDoc
     */
    public function checkIndexName($name): bool
    {
        return $this->db->checkIndexName($name);
    }


    /**
     * @inheritDoc
     */
    public function checkTableName($a_name): bool
    {
        return $this->db->checkTableName($a_name);
    }


    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }


    /**
     * @inheritDoc
     */
    public function concat(array $values, $allow_null = true): string
    {
        return $this->db->concat($values, $allow_null);
    }


    /**
     * @inheritDoc
     */
    public function connect($return_false_on_error = false): ?bool
    {
        return $this->connect($return_false_on_error);
    }


    /**
     * @inheritDoc
     */
    public function constraintName($a_table, $a_constraint): string
    {
        return $this->db->constraintName($a_table, $a_constraint);
    }


    /**
     * @inheritDoc
     */
    public function createDatabase($a_name, $a_charset = "utf8", $a_collation = ""): bool
    {
        return $this->db->createDatabase($a_name, $a_charset, $a_collation);
    }


    /**
     * @inheritDoc
     */
    public function createSequence($table_name, $start = 1): bool
    {
        $this->db->createSequence($table_name, $start);
    }


    /**
     * @inheritDoc
     */
    public function createTable($table_name, $fields, $drop_table = false, $ignore_erros = false): bool
    {
        return $this->db->createTable($table_name, $fields, $drop_table, $ignore_erros);
    }


    /**
     * @inheritDoc
     */
    public function doesCollationSupportMB4Strings(): bool
    {
        return $this->db->doesCollationSupportMB4Strings();
    }


    /**
     * @inheritDoc
     */
    public function dropFulltextIndex($a_table, $a_name): bool
    {
        return $this->db->dropFulltextIndex($a_table, $a_name);
    }


    /**
     * @inheritDoc
     */
    public function dropIndex($a_table, $a_name = "i1"): bool
    {
        return $this->db->dropIndex($a_table, $a_name);
    }


    /**
     * @inheritDoc
     */
    public function dropIndexByFields($table_name, $afields): bool
    {
        return $this->db->dropIndexByFields($table_name, $afields);
    }


    /**
     * @inheritDoc
     */
    public function dropPrimaryKey($table_name): bool
    {
        $this->db->dropPrimaryKey($table_name);
    }


    /**
     * @param $table_name string
     */
    public function dropSequence($table_name): bool
    {
        $this->db->dropSequence($table_name);
    }


    /**
     * @inheritDoc
     */
    public function dropTable($table_name, $error_if_not_existing = true): bool
    {
        return $this->db->dropTable($table_name, $error_if_not_existing);
    }


    /**
     * @inheritDoc
     */
    public function dropTableColumn($table_name, $column_name): bool
    {
        $this->db->dropTableColumn($table_name, $column_name);
    }


    /**
     * @inheritDoc
     */
    public function dropUniqueConstraint($table, $name = "con"): bool
    {
        return $this->db->dropUniqueConstraint($table, $name);
    }


    /**
     * @inheritDoc
     */
    public function dropUniqueConstraintByFields($table, $fields): bool
    {
        return $this->db->dropUniqueConstraintByFields($table, $fields);
    }


    /**
     * @inheritDoc
     */
    public function enableResultBuffering($a_status): void
    {
        $this->db->enableResultBuffering($a_status);
    }


    /**
     * @inheritDoc
     */
    public function equals($columns, $value, $type, $emptyOrNull = false): string
    {
        return $this->db->equals($columns, $value, $type, $emptyOrNull);
    }


    /**
     * @inheritDoc
     */
    public function escape($value, $escape_wildcards = false): string
    {
        return $this->db->escape($value, $escape_wildcards);
    }


    /**
     * @inheritDoc
     */
    public function escapePattern($text): string
    {
        return $this->db->escapePattern($text);
    }


    /**
     * @inheritDoc
     */
    public function execute($stmt, $data = array()): \ilDBStatement
    {
        return $this->db->execute($stmt, $data);
    }


    /**
     * @inheritDoc
     */
    public function executeMultiple($stmt, $data): array
    {
        $this->db->executeMultiple($stmt, $data);
    }


    /**
     * @inheritDoc
     */
    public function fetchAll($query_result, $fetch_mode = ilDBConstants::FETCHMODE_ASSOC): array
    {
        return $this->db->fetchAll($query_result, $fetch_mode = ilDBConstants::FETCHMODE_ASSOC);
    }


    /**
     * @inheritDoc
     */
    public function fetchAssoc($query_result): ?array
    {
        return $this->db->fetchAssoc($query_result);
    }


    /**
     * @inheritDoc
     */
    public function fetchObject($query_result): ?\stdClass
    {
        return $this->db->fetchObject($query_result);
    }


    /**
     * @inheritDoc
     */
    public function free($a_st): void
    {
        $this->db->free($a_st);
    }


    /**
     * @inheritDoc
     */
    public function fromUnixtime($expr, $to_text = true): string
    {
        return $this->db->fromUnixtime($expr, $to_text);
    }


    /**
     * @inheritDoc
     */
    public function getAllowedAttributes(): array
    {
        return $this->db->getAllowedAttributes();
    }


    /**
     * @inheritDoc
     */
    public function getDBType(): string
    {
        return $this->db->getDBType();
    }


    /**
     * @inheritDoc
     */
    public function getDBVersion(): string
    {
        return $this->db->getDBVersion();
    }


    /**
     * @inheritDoc
     */
    public function getDSN(): string
    {
        return $this->db->getDSN();
    }


    /**
     * @inheritDoc
     */
    public function getLastInsertId(): int
    {
        return $this->db->getLastInsertId();
    }


    /**
     * @inheritDoc
     */
    public function getPrimaryKeyIdentifier(): string
    {
        return $this->db->getPrimaryKeyIdentifier();
    }


    /**
     * @inheritDoc
     */
    public function getSequenceName($table_name): string
    {
        return $this->db->getSequenceName($table_name);
    }


    /**
     * @inheritDoc
     */
    public function getServerVersion($native = false): int
    {
        return $this->db->getServerVersion($native);
    }


    /**
     * @inheritDoc
     */
    public function getStorageEngine(): string
    {
        return $this->db->getStorageEngine();
    }


    /**
     * @inheritDoc
     */
    public function groupConcat($a_field_name, $a_seperator = ",", $a_order = null): string
    {
        return $this->db->groupConcat($a_field_name, $a_seperator, $a_order);
    }


    /**
     * @inheritDoc
     */
    public function in($field, $values, $negate = false, $type = ""): string
    {
        return $this->db->in($field, $values, $negate, $type);
    }


    /**
     * @inheritDoc
     */
    public function indexExistsByFields($table_name, $fields): bool
    {
        return $this->db->indexExistsByFields($table_name, $fields);
    }


    /**
     * @inheritDoc
     */
    public function initFromIniFile($tmpClientIniFile = null): void
    {
        $this->db->initFromIniFile($tmpClientIniFile);
    }


    /**
     * @inheritDoc
     */
    public function insert($table_name, $values): int
    {
        return $this->db->insert($table_name, $values);
    }


    /**
     * @inheritDoc
     */
    public function isFulltextIndex($a_table, $a_name): bool
    {
        return $this->db->isFulltextIndex($a_table, $a_name);
    }


    /**
     * @inheritDoc
     */
    public function like($column, $type, $value = "?", $case_insensitive = true): string
    {
        return $this->db->like($column, $type, $value, $case_insensitive);
    }


    /**
     * @inheritDoc
     */
    public function listSequences(): array
    {
        return $this->db->listSequences();
    }


    /**
     * @inheritDoc
     */
    public function listTables(): array
    {
        return $this->db->listTables();
    }


    /**
     * @inheritDoc
     *
     * @internal
     */
    public function loadModule($module): \ilDBManager|\ilDBReverse
    {
        return $this->db->loadModule($module);
    }


    /**
     * @inheritDoc
     */
    public function locate($a_needle, $a_string, $a_start_pos = 1): string
    {
        return $this->db->locate($a_needle, $a_string, $a_start_pos);
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function lockTables($tables): void
    {
        $this->db->lockTables($tables);
    }


    /**
     * @inheritDoc
     */
    public function lower($a_exp): string
    {
        return $this->db->lower($a_exp);
    }


    /**
     * @inheritDoc
     */
    public function manipulate($query): int
    {
        return $this->db->manipulate($query);
    }


    /**
     * @inheritDoc
     */
    public function manipulateF($query, $types, $values): int
    {
        return $this->db->manipulateF($query, $types, $values);
    }


    /**
     * @inheritDoc
     */
    public function migrateAllTablesToCollation($collation = ilDBConstants::MYSQL_COLLATION_UTF8MB4): array
    {
        return $this->db->migrateAllTablesToCollation($collation);
    }


    /**
     * @inheritDoc
     */
    public function migrateAllTablesToEngine($engine = ilDBConstants::MYSQL_ENGINE_INNODB): array
    {
        return $this->db->migrateAllTablesToEngine($engine);
    }


    /**
     * @inheritDoc
     */
    public function modifyTableColumn($table, $column, $attributes): bool
    {
        return $this->db->modifyTableColumn($table, $column, $attributes);
    }


    /**
     * @inheritDoc
     */
    public function nextId($table_name): int
    {
        return $this->db->nextId($table_name);
    }


    /**
     * @inheritDoc
     */
    public function now(): string
    {
        return $this->db->now();
    }


    /**
     * @inheritDoc
     */
    public function numRows($query_result): int
    {
        return $this->db->numRows($query_result);
    }


    /**
     * @inheritDoc
     */
    public function prepare($a_query, $a_types = null, $a_result_types = null): \ilDBStatement
    {
        return $this->db->prepare($a_query, $a_types, $a_result_types);
    }


    /**
     * @inheritDoc
     */
    public function prepareManip($a_query, $a_types = null): \ilDBStatement
    {
        return $this->db->prepareManip($a_query, $a_types);
    }


    /**
     * @inheritDoc
     */
    public function query($query): \ilDBStatement
    {
        return $this->db->query($query);
    }


    /**
     * @inheritDoc
     */
    public function queryCol($query, $type = ilDBConstants::FETCHMODE_DEFAULT, $colnum = 0): array
    {
        return $this->db->queryCol($query, $type, $colnum);
    }


    /**
     * @inheritDoc
     */
    public function queryF($query, $types, $values): \ilDBStatement
    {
        return $this->db->queryF($query, $types, $values);
    }


    /**
     * @inheritDoc
     */
    public function queryRow($query, $types = null, $fetchmode = ilDBConstants::FETCHMODE_DEFAULT): array
    {
        return $this->db->queryRow($query, $types, $fetchmode);
    }


    /**
     * @inheritDoc
     */
    public function quote($value, $type): string
    {
        return $this->db->quote($value, $type);
    }


    /**
     * @inheritDoc
     */
    public function quoteIdentifier($identifier, $check_option = false): string
    {
        return $this->db->quoteIdentifier($identifier, $check_option);
    }


    /**
     * @param $old_name
     * @param $new_name
     *
     * @return mixed
     */
    public function renameTable($old_name, $new_name): bool
    {
        return $this->db->renameTable($old_name, $new_name);
    }


    /**
     * @inheritDoc
     */
    public function renameTableColumn($table_name, $column_old_name, $column_new_name): bool
    {
        $this->db->renameTableColumn($table_name, $column_old_name, $column_new_name);
    }


    /**
     * @inheritDoc
     */
    public function replace($table, $primaryKeys, $otherColumns): int
    {
        $this->db->replace($table, $primaryKeys, $otherColumns);
    }


    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        return $this->db->rollback();
    }


    /**
     * @inheritDoc
     */
    public function sanitizeMB4StringIfNotSupported($query): string
    {
        return $this->db->sanitizeMB4StringIfNotSupported($query);
    }


    /**
     * @inheritDoc
     */
    public function sequenceExists($sequence): bool
    {
        return $this->db->sequenceExists($sequence);
    }


    /**
     * @inheritDoc
     */
    public function setDBHost($host): void
    {
        $this->db->setDBHost($host);
    }


    /**
     * @inheritDoc
     */
    public function setDBPassword($password): void
    {
        $this->db->setDBPassword($password);
    }


    /**
     * @inheritDoc
     */
    public function setDBPort($port): void
    {
        $this->db->setDBPort($port);
    }


    /**
     * @inheritDoc
     */
    public function setDBUser($user): void
    {
        $this->db->setDBUser($user);
    }


    /**
     * @inheritDoc
     */
    public function setLimit(int $limit, int $offset = 0): void
    {
        $this->db->setLimit($limit, $offset);
    }


    /**
     * @inheritDoc
     */
    public function setStorageEngine($storage_engine): void
    {
        $this->db->setStorageEngine($storage_engine);
    }


    /**
     * @inheritDoc
     */
    public function substr($a_exp): string
    {
        return $this->db->substr($a_exp);
    }


    /**
     * @inheritDoc
     */
    public function supports($feature): bool
    {
        return $this->db->supports($feature);
    }


    /**
     * @inheritDoc
     */
    public function supportsCollationMigration(): bool
    {
        return $this->db->supportsCollationMigration();
    }


    /**
     * @inheritDoc
     */
    public function supportsEngineMigration(): bool
    {
        return $this->db->supportsEngineMigration();
    }


    /**
     * @inheritDoc
     */
    public function supportsFulltext(): bool
    {
        return $this->db->supportsFulltext();
    }


    /**
     * @inheritDoc
     */
    public function supportsSlave(): bool
    {
        return $this->db->supportsSlave();
    }


    /**
     * @inheritDoc
     */
    public function supportsTransactions(): bool
    {
        return $this->db->supportsTransactions();
    }


    /**
     * @inheritDoc
     */
    public function tableColumnExists($table_name, $column_name): bool
    {
        return $this->db->tableColumnExists($table_name, $column_name);
    }


    /**
     * @inheritDoc
     */
    public function tableExists($table_name): bool
    {
        return $this->db->tableExists($table_name);
    }


    /**
     * @inheritDoc
     */
    public function uniqueConstraintExists($table, array $fields): bool
    {
        return $this->db->uniqueConstraintExists($table, $fields);
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public function unixTimestamp(): string
    {
        return $this->db->unixTimestamp();
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function unlockTables(): void
    {
        $this->db->unlockTables();
    }


    /**
     * @inheritDoc
     */
    public function update($table_name, $values, $where): int
    {
        return $this->db->update($table_name, $values, $where);
    }


    /**
     * @inheritDoc
     */
    public function upper($a_exp): string
    {
        return $this->db->upper($a_exp);
    }


    /**
     * @inheritDoc
     */
    public function useSlave($bool): bool
    {
        return $this->db->useSlave($bool);
    }
}
