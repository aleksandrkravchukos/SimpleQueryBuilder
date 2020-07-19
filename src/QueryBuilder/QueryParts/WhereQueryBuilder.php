<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

class WhereQueryBuilder implements QueryPartsBuilderInterface
{
    /**
     * @param string|array $conditions
     * @return string
     */
    public function build($conditions): string
    {
        $where = '';

        if (is_array($conditions) && count($conditions) == 4) {
            $where = sprintf("%s %s %s '%s' ", $conditions[0], $conditions[1], $conditions[2], $conditions[3]);
        }

        if (is_string($conditions)) {
            $where = sprintf('%s ', $conditions);
        }

        return $where;
    }
}