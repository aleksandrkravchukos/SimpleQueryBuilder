<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder;

interface FromQueryBuilderInterface
{
    public function build($fields): string;
}
