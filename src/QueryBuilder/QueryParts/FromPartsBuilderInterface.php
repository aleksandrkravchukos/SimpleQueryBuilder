<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

interface FromPartsBuilderInterface
{
    public function build($fields): string;
}
