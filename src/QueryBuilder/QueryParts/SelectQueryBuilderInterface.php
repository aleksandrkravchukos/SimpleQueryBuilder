<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder;

interface SelectQueryBuilderInterface
{
    public function build($fields): string;
}
