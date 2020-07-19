<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

interface QueryPartsBuilderInterface
{
    public function build($fields): string;
}
