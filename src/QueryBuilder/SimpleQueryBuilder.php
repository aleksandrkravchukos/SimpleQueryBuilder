<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder;

use MySimpleQueryBuilder\QueryBuilder\Exception\LogicException;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\FromPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\GroupByPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\HavingPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\OrderByPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\SelectPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\WherePartsBuilderInterface;

class SimpleQueryBuilder implements SimpleQueryBuilderInterface
{
    private string $query   = '';
    private string $select  = '';
    private string $from    = '';
    private string $where   = '';
    private string $groupBy = '';
    private string $having  = '';
    private string $orderBy = '';
    private $limit          = null;
    private $offset         = null;

    private SelectPartsBuilderInterface $selectQueryBuilder;
    private FromPartsBuilderInterface $fromQueryBuilder;
    private WherePartsBuilderInterface $whereQueryBuilder;
    private GroupByPartsBuilderInterface $groupByQueryBuilder;
    private OrderByPartsBuilderInterface $orderByQueryBuilder;
    private HavingPartsBuilderInterface $havingQueryBuilder;


    public function __construct(
        SelectPartsBuilderInterface $selectQueryBuilder,
        FromPartsBuilderInterface $fromQueryBuilder,
        WherePartsBuilderInterface $whereQueryBuilder,
        GroupByPartsBuilderInterface $groupByQueryBuilder,
        OrderByPartsBuilderInterface $orderByQueryBuilder,
        HavingPartsBuilderInterface $havingQueryBuilder
    )
    {
        $this->selectQueryBuilder  = $selectQueryBuilder;
        $this->fromQueryBuilder    = $fromQueryBuilder;
        $this->whereQueryBuilder   = $whereQueryBuilder;
        $this->groupByQueryBuilder = $groupByQueryBuilder;
        $this->orderByQueryBuilder = $orderByQueryBuilder;
        $this->havingQueryBuilder  = $havingQueryBuilder;
    }

    /**
     * @param array|string $fields
     * @return SimpleQueryBuilderInterface
     */
    public function select($fields): SimpleQueryBuilderInterface
    {
        $this->select = $this->selectQueryBuilder->build($fields);

        return $this;
    }

    /**
     * @param string|SimpleQueryBuilderInterface|array<string|SimpleQueryBuilderInterface> $tables
     * @return SimpleQueryBuilderInterface
     */
    public function from($tables): SimpleQueryBuilderInterface
    {

        $this->from = $this->fromQueryBuilder->build($tables);

        return $this;
    }

    /**
     * @param string|array $conditions
     * @return SimpleQueryBuilderInterface
     */
    public function where($conditions): SimpleQueryBuilderInterface
    {
        $this->where .= $this->whereQueryBuilder->build($conditions);

        return $this;
    }

    /**
     * @param string|array $fields
     * @return SimpleQueryBuilderInterface
     */
    public function groupBy($fields): SimpleQueryBuilderInterface
    {
        $this->groupBy = $this->groupByQueryBuilder->build($fields);

        return $this;
    }

    /**
     * @param string|array $conditions
     * @return SimpleQueryBuilderInterface
     */
    public function having($conditions): SimpleQueryBuilderInterface
    {
        $this->having = $this->havingQueryBuilder->build($conditions);

        return $this;
    }

    /**
     * @param string|array $fields
     * @return SimpleQueryBuilderInterface
     */
    public function orderBy($fields): SimpleQueryBuilderInterface
    {

        $this->orderBy = $this->orderByQueryBuilder->build($fields);

        return $this;
    }

    /**
     * @param int $limit
     * @return SimpleQueryBuilderInterface
     */
    public function limit($limit): SimpleQueryBuilderInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $offset
     * @return SimpleQueryBuilderInterface
     */
    public function offset($offset): SimpleQueryBuilderInterface
    {

        $this->offset = $offset;

        return $this;
    }

    /**
     * @return string
     * @throws LogicException
     */
    public function build(): string
    {
        if ((!is_null($this->limit) && !is_integer($this->limit)) || $this->limit < 0) {
            throw new LogicException('Limit can be only integer and more than 0');
        }

        if ((!is_null($this->offset) && !is_integer($this->offset)) || $this->offset < 0) {
            throw new LogicException('Offset can be only integer and more or equal than 0');
        }

        if (!$this->select) {
            throw new LogicException("Type of SELECT parameter is incorrect. This can be only array or string and can not be empty");
        }

        if (!$this->from) {
            throw new LogicException("FROM parameter is incorrect or can not be empty");
        }

        if ($this->where == 'incorrect') {
            throw new LogicException("The parameter WHERE type is not array or is not string");
        }

        if ($this->groupBy == 'incorrect') {
            throw new LogicException("The parameter GROUP BY type is not array or is not string");
        }

        if ($this->orderBy == 'incorrect') {
            throw new LogicException("The parameter ORDER BY type is not array or is not string");
        }

        if ($this->having == 'incorrect') {
            throw new LogicException("The parameter HAVING type is not array or is not string");
        }

        if ($this->select !== '') {
            $this->query .= sprintf("SELECT %s ", trim($this->select));
        }

        if ($this->from !== '') {
            $this->query .= sprintf("FROM %s ", trim($this->from));
        }

        if ($this->where !== '') {
            $this->query .= sprintf("WHERE %s ", trim($this->where));
        }

        if ($this->groupBy !== '') {
            $this->query .= sprintf("GROUP BY %s ", trim($this->groupBy));
        }

        if ($this->having !== '') {
            $this->query .= sprintf("HAVING %s ", trim($this->having));
        }

        if ($this->orderBy !== '') {
            $this->query .= sprintf("ORDER BY %s ", $this->orderBy);
        }

        if ($this->limit !== null) {
            $this->query .= sprintf("LIMIT %d ", $this->limit);
        }

        if ($this->offset !== null) {
            $this->query .= sprintf("OFFSET %d ", $this->offset);
        }

        return trim($this->query);
    }

    /**
     * @return string
     * @throws LogicException
     */
    public function buildCount(): string
    {
        $result = [];

        if (!$this->select) {
            throw new LogicException('SELECT values can be only string or array and can not be empty');
        }

        $selects = explode(',', $this->select);

        $i = 0;
        foreach ($selects as $select) {
            $result[] = sprintf("COUNT(%s) as field_count_$select", $select);
            $i++;
        }

        $this->select = implode(',', $result);

        return $this->select;
    }
}
