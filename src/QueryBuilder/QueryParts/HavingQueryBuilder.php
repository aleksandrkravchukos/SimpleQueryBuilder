<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

class HavingQueryBuilder implements QueryPartsBuilderInterface
{
    /**
     * @param string|array $conditions
     * @return string
     */
    public function build($conditions): string
    {
        $having = '';
        if (is_array($conditions) && count($conditions) == 4) {
            $having = sprintf(' %s(%s) %s %d ', $conditions[0], $conditions[1], $conditions[2], $conditions[3]);
        }

        if (is_string($conditions)) {
            $having = sprintf(' %s ', $conditions);
        }

        if (!is_string($conditions) && !is_array($conditions) && !empty($conditions)) {
            $having = 'incorrect';
        }

        return $having;
    }
}