<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder;

use MySimpleQueryBuilder\QueryBuilder\Exception\LogicException;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\FromQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\QueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\SelectQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\WhereQueryBuilder;

class SimpleQueryBuilder implements SimpleQueryBuilderInterface
{
    private string $query  = '';
    private string $select = '';
    private string $from   = '';
    private string $where  = '';
    private array $groupBy = [];
    private string $having = '';
    private array $orderBy = [];
    private $limit         = null;
    private $offset        = null;
    private array $errors  = [];

    private SelectQueryBuilder $selectQueryBuilder;
    private FromQueryBuilder $fromQueryBuilder;
    private WhereQueryBuilder $whereQueryBuilder;

    public function __construct(
        SelectQueryBuilder $selectQueryBuilder,
        FromQueryBuilder $fromQueryBuilder,
        WhereQueryBuilder $whereQueryBuilder
    )
    {
        $this->selectQueryBuilder = $selectQueryBuilder;
        $this->fromQueryBuilder   = $fromQueryBuilder;
        $this->whereQueryBuilder  = $whereQueryBuilder;
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
        $fieldArray = [];
        if (is_array($fields)) {
            $fieldArray = $fields;
        }

        if (is_string($fields)) {
            $fieldArray = explode(',', trim($fields));
        }

        $this->groupBy = array_merge($this->groupBy, $fieldArray);

        return $this;
    }

    /**
     * @param string|array $conditions
     * @return SimpleQueryBuilderInterface
     */
    public function having($conditions): SimpleQueryBuilderInterface
    {
        if (is_array($conditions) && count($conditions) == 4) {
            $this->having .= sprintf(' %d(%d) %d %d ', $conditions[0], $conditions[1], $conditions[2], $conditions[3]);
        }

        if (is_string($conditions)) {
            $this->having .= sprintf(' %d ', $conditions);
        }

        return $this;
    }

    /**
     * @param string|array $fields
     * @return SimpleQueryBuilderInterface
     */
    public function orderBy($fields): SimpleQueryBuilderInterface
    {

        $fieldArray = [];
        if (is_array($fields)) {
            $fieldArray = $fields;
        }

        if (is_string($fields)) {
            $fieldArray = explode(',', trim($fields));
        }

        $this->orderBy = array_merge($this->orderBy, $fieldArray);

        return $this;
    }

    /**
     * @param int $limit
     * @return SimpleQueryBuilderInterface
     */
    public function limit($limit): SimpleQueryBuilderInterface
    {
        if (!is_integer($limit)) {
            $this->errors['errorLimit'] = 'Type of LIMIT parameter is incorrect. This can be only integer';
        }
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $offset
     * @return SimpleQueryBuilderInterface
     */
    public function offset($offset): SimpleQueryBuilderInterface
    {
        if (!is_integer($offset)) {
            $this->errors['errorOffset'] = 'Type of OFFSET parameter is incorrect. This can be only integer';
        }

        $this->offset = $offset;

        return $this;
    }

    /**
     * @return string
     * @throws LogicException
     */
    public function build(): string
    {

        if ($this->select === []) {
            throw new LogicException('The parameter SELECT is not filled');
        }

        if (isset($this->errors['selectError'])) {
            throw new LogicException($this->errors['selectError']);
        }

        if (isset($this->errors['errorLimit'])) {
            throw new LogicException($this->errors['errorLimit']);
        }

        if (isset($this->errors['errorOffset'])) {
            throw new LogicException($this->errors['errorOffset']);
        }

        if (!$this->select) {
            throw new LogicException("Type of SELECT parameter is incorrect. This can be only array or string");
        }

        if (!$this->from) {
            throw new LogicException("FROM parameter is incorrect or can not be empty");
        }

        if (!$this->where) {
            throw new LogicException("The parameter WHERE type is not array or is not string");
        }

        $this->query = $this->query = sprintf(
            "SELECT %s FROM %s ",
            $this->select,
            $this->from,
            );

//        var_dump($this->where);exit();

        if ($this->where !== '') {
            $this->query .= sprintf("WHERE %s ", trim($this->where));
        }

        if ($this->groupBy) {
            $this->query .= sprintf("GROUP BY %s ", trim(implode(',', $this->groupBy)));
        }

        if ($this->having !== '') {
            $this->query .= sprintf("HEAVING %s ", $this->having);
        }

        if ($this->orderBy) {
            $this->query .= sprintf("ORDER BY %s ", implode(',', $this->orderBy));
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
            throw new LogicException('SELECT values can be only string or array');
        }

        $selects = explode(',', $this->select);

        foreach ($selects as $select) {
            $result[] = sprintf('count(%s)', $select);
        }

        $this->select = implode(',', $result);

        return $this->select;
    }
}
