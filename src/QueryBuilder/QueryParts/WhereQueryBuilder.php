<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

class WhereQueryBuilder implements WherePartsBuilderInterface
{
    /**
     * @param string|array $conditions
     * @return string
     */
    public function build($conditions): string
    {
        $where = '';

        if (is_array($conditions)) {
            if (count($conditions) === 4) {
                $where = sprintf("%s %s %s '%s' ", $conditions[0], $conditions[1], $conditions[2], $conditions[3]);
            }
        }

        if (is_string($conditions)) {
            $where = sprintf('%s ', $conditions);
        }

        if (!is_string($conditions) && !is_array($conditions) && !empty($conditions)) {
            $where = 'incorrect';
        }

        return $where;
    }
}