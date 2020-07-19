<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

class GroupByQueryBuilder implements QueryPartsBuilderInterface
{
    /**
     * @param string|array $fields
     * @return string
     */
    public function build($fields): string
    {
        $groupBy = [];
        if (is_array($fields)) {
            $groupBy = implode(',', $fields);
        }

        if (is_string($fields)) {
            $groupBy = trim($fields);
        }

        if (!is_string($fields) && !is_array($fields) && !empty($fields)) {
            $groupBy = 'incorrect';
        }

        return $groupBy;
    }
}