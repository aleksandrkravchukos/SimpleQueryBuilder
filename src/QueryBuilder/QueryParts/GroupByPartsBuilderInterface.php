<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

interface GroupByPartsBuilderInterface
{
    public function build($fields): string;
}
