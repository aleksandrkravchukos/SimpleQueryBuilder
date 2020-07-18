<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder;

use MySimpleQueryBuilder\QueryBuilder\Exception\LogicException;

class SimpleQueryBuilder implements SimpleQueryBuilderInterface
{
    /**
     * @var string
     */
    private string $query = '';
    private array $select = [];
    private string $from = '';
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

        if (!is_array($fields) && !is_string($fields)) {
            $this->select = ['empty'];
            $this->errors['selectError'] = 'Type of SELECT parameter is incorrect. This can be only array or string';
        }

        return $this;
    }

    /**
     * @param string|SimpleQueryBuilderInterface|array<string|SimpleQueryBuilderInterface> $tables
     * @return SimpleQueryBuilderInterface
     */
    public function from($tables): SimpleQueryBuilderInterface
    {
        if (is_string($tables)) {
            $this->from = $tables;
        }

        if ($tables instanceof SimpleQueryBuilderInterface) {
            $this->from = $tables->build();
        }

        if (is_array($tables)) {
            foreach ($tables as $table) {
                if ($table instanceof SimpleQueryBuilderInterface) {
                    if ($this->from !== '') {
                        $this->from = $table->build();
                    } else {
                        $this->from .= $table->build() . ',';
                    }
                }

                if (is_string($table)) {
                    if ($this->from !== '') {
                        $this->from = $table;
                    } else {
                        $this->from .= $table . ',';
                    }
                }
            }

            if ($this->from[strlen($this->from) - 1] == ',') {
                $this->from = substr($this->from, 0, strlen($this->from) - 1);
            }
        }

        if (!is_array($tables) && !($tables instanceof SimpleQueryBuilderInterface) && !(is_string($tables))) {
            $this->from = 'empty';
            $this->errors['fromError'] = 'Type of parameters FROM is incorrect';
        }

        return $this;
    }

    /**
     * @param string|array $conditions
     * @return SimpleQueryBuilderInterface
     */
    public function where($conditions): SimpleQueryBuilderInterface
    {
        if (is_array($conditions) && count($conditions) == 4) {
            $this->where .= sprintf("%s %s %s '%s' ", $conditions[0], $conditions[1], $conditions[2], $conditions[3]);
        }

        if (is_string($conditions)) {
            $this->where .= sprintf('%s ', $conditions);
        }

        if (!is_string($conditions) && !is_array($conditions)) {
            $this->errors['where'] = 'The parameter WHERE type is not array or is not string';
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
        if ($this->from === '') {
            throw new LogicException('The parameter FROM is not filled');
        }

        if ($this->select === []) {
            throw new LogicException('The parameter SELECT is not filled');
        }

        if (isset($this->errors['where'])) {
            throw new LogicException($this->errors['where']);
        }

        if (isset($this->errors['fromError'])) {
            throw new LogicException($this->errors['fromError']);
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

        $this->query = $this->query = sprintf(
            "SELECT %s FROM %s ",
            implode(',', $this->select),
            $this->from,
            );


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

        if (!is_string($this->select) && !is_array($this->select)) {
            throw new LogicException('SELECT values can be only string or array');
        }

        foreach ($this->select as $select) {
            $result[] = sprintf('count(%s)', $select);
        }

        $this->select = $result;

        return implode(',', $result);
    }
}
