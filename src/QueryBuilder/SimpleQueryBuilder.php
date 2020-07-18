<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder;

class SimpleQueryBuilder implements SimpleQueryBuilderInterface
{
    /**
     * @var string
     */
    private string $query = '';
    private array $select = [];
    private array $from = [];
    private string $where = '';
    private array $groupBy = [];
    private string $having = '';
    private array $orderBy = [];
    private $limit = null;
    private $offset = null;

    private array $errors;

    /**
     * @param array|string $fields
     * @return SimpleQueryBuilderInterface
     */
    public function select($fields): SimpleQueryBuilderInterface
    {

        $selectArray = [];
        if (is_array($fields)) {
            $selectArray = $fields;
        }

        if (is_string($fields)) {
            $selectArray = explode(',', trim($fields));
        }

        $this->select = array_merge($this->select, $selectArray);

        return $this;
    }

    /**
     * @param string|SimpleQueryBuilderInterface|array<string|SimpleQueryBuilderInterface> $tables
     * @return SimpleQueryBuilderInterface
     */
    public function from($tables): SimpleQueryBuilderInterface
    {
        $tableArray = [];
        if (is_array($tables)) {
            $tableArray = $tables;
        }

        if (is_string($tables)) {
            $tableArray = explode(',', trim($tables));
        }

        $this->from = array_merge($this->from, $tableArray);

        return $this;
    }

    /**
     * @param string|array $conditions
     * @return SimpleQueryBuilderInterface
     */
    public function where($conditions): SimpleQueryBuilderInterface
    {
        if (is_array($conditions) && count($conditions) == 4) {
            $this->where .= sprintf(" %s %s %s '%s' ", $conditions[0], $conditions[1], $conditions[2], $conditions[3]);
        }

        if (is_string($conditions)) {
            $this->where .= sprintf(' %s ', $conditions);
        }

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



        $this->query = $this->query = sprintf(
            "SELECT %s FROM %s ",
            implode(',', $this->select),
            implode(',', $this->from),
            );

        if ($this->where !== '') {
            $this->query .= sprintf(" WHERE %s ", $this->where);
        }

        if ($this->groupBy) {
            $this->query .= sprintf(" GROUP BY %s ", implode(',', $this->groupBy));
        }

        if ($this->having !== '') {
            $this->query .= sprintf(" HEAVING %s ", $this->having);
        }

        if ($this->orderBy) {
            $this->query .= sprintf(" ORDER BY %s ", implode(',', $this->orderBy));
        }

        if ($this->limit !== null) {
            $this->query .= sprintf(" LIMIT %d", $this->limit);
        }

        if ($this->offset !== null) {
            $this->query .= sprintf(" OFFSET %d", $this->offset);
        }

        return $this->query;
    }

    /**
     * @return string
     * @throws LogicException
     */
    public function buildCount(): string
    {

    }
}
