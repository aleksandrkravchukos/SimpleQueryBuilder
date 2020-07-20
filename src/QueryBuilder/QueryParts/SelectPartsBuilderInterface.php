<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

interface SelectPartsBuilderInterface
{
    public function build($fields): string;
}
