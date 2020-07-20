<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;


class SelectQueryBuilder implements SelectPartsBuilderInterface
{
    /**
     * @param array|string $fields
     * @return string
     */
    public function build($fields): string
    {
        $select = '';
        if (is_array($fields)) {
            $select = implode(',', $fields);
        }

        if (is_string($fields)) {
            $select = $fields;
        }

        return $select;
    }
}