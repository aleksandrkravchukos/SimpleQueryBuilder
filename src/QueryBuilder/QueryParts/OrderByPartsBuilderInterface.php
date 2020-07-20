<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

interface OrderByPartsBuilderInterface
{
    public function build($fields): string;
}
