<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

class OrderByQueryBuilder implements OrderByPartsBuilderInterface
{
    /**
     * @param string|array $fields
     * @return string
     */
    public function build($fields): string
    {
        $orderBy = '';
        if (is_array($fields)) {
            $orderBy = implode(',', $fields);
        }

        if (is_string($fields)) {
            $orderBy = trim($fields);
        }

        if (!is_string($fields) && !is_array($fields) && !empty($fields)) {
            $orderBy = 'incorrect';
        }

        return $orderBy;
    }
}