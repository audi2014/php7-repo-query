<?php
/**
 * Created by PhpStorm.
 * User: andriyprosekov
 * Date: 27/07/2018
 * Time: 15:13
 */


namespace Audi2014\RepoRequestQuery;

use Audi2014\RequestQuery\RequestQueryInterface;
use Audi2014\RequestQuery\RequestQueryPageInterface;

abstract class AbstractRepoRequestQuery implements RepoRequestQueryInterface {

    public function fetchQueryPageItems(RequestQueryPageInterface $query): array {
        $sql = <<<MySQL
SELECT {$this->getFieldsSql()}
FROM {$this->getTable()}
{$this->getJoinsSql()}
{$query->getWhereSql()}
{$this->getGroupBySql()}
{$query->getHavingSql()}
{$query->getOrderBySql()}
limit {$query->getOffset()}, {$query->getCount()}
MySQL;
        $data = $this->fetchAllBySqlAndArgs(
            $sql,
            $query->getExecuteValues()
        );
        return $data;

    }

    public function deleteQueryItems(RequestQueryInterface $query, ?int $count = 0): int {
        if ($count) $count = "LIMIT $count";
        else $count = "";

        $sql = <<<MySQL
DELETE FROM {$this->getTable()}
{$query->getWhereSql()}
{$query->getHavingSql()}
$count
MySQL;
        return $this->deleteRowsBySql(
            $sql,
            $query->getExecuteValues()
        );

    }

    public function fetchQueryItems(RequestQueryInterface $query, ?int $count = 0): array {
        if ($count) $count = "LIMIT $count";
        else $count = "";

        $sql = <<<MySQL
SELECT {$this->getFieldsSql()}
FROM {$this->getTable()}
{$this->getJoinsSql()}
{$query->getWhereSql()}
{$this->getGroupBySql()}
{$query->getHavingSql()}
{$query->getOrderBySql()}
$count
MySQL;
        $data = $this->fetchAllBySqlAndArgs(
            $sql,
            $query->getExecuteValues()
        );
        return $data;

    }

    public function fetchQueryItem(RequestQueryInterface $query) {
        $sql = <<<MySQL
SELECT {$this->getFieldsSql()}
FROM {$this->getTable()}
{$this->getJoinsSql()}
{$query->getWhereSql()}
{$this->getGroupBySql()}
{$query->getHavingSql()}
{$query->getOrderBySql()}
LIMIT 0,1
MySQL;
        $data = $this->fetchFirstBySqlAndArgs(
            $sql,
            $query->getExecuteValues()
        );
        return $data;
    }

    /**
     * @param RequestQueryInterface $query
     * @return int
     * @throws \Exception
     */
    public function fetchQueryCount(RequestQueryInterface $query): int {

        if (!empty($this->getFieldsForCount())) {
            $fieldsForCountSql = "{$this->getGroupBy()} as count_id, " . implode(', ', $this->getFieldsForCount());
            $sql = /** @lang MySQL */
                <<<SQL
SELECT count(DISTINCT selection.count_id) as `count` FROM (
    SELECT $fieldsForCountSql
    FROM {$this->getTable()}
    {$this->getJoinsSql()}
    {$query->getWhereSql()}
    {$this->getGroupByForCountSql()}
    {$query->getHavingSql()}
) as selection
SQL;
        } else {
            $sql = /** @lang MySQL */
                <<<SQL
SELECT count(DISTINCT {$this->getGroupBy()}) as `count`
FROM {$this->getTable()}
{$this->getJoinsSql()}
{$query->getWhereSql()}
{$this->getGroupByForCountSql()}
SQL;
        }


        $stmt = $this->prepare($sql);
        $this->execute($stmt, $query->getExecuteValues());
        $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $r_count = count($rows);
        if ($r_count !== 1) {
            throw new \Exception("bad fetchQueryCount sql: count of returned counters !== 1. returned: ($r_count)");
        }
        return reset($rows);
    }



}