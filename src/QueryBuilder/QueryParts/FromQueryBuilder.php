<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilderInterface;

class FromQueryBuilder implements QueryPartsBuilderInterface
{
    /**
     * @param string|SimpleQueryBuilderInterface|array<string|SimpleQueryBuilderInterface> $tables
     * @return string
     */
    public function build($tables): string
    {
        $from = '';
        if (is_string($tables)) {
            $from = $tables;
        }

        if ($tables instanceof SimpleQueryBuilderInterface) {
            $from = $tables->build();
        }

        if (is_array($tables)) {
            foreach ($tables as $table) {
                if ($table instanceof SimpleQueryBuilderInterface) {
                    if ($from !== '') {
                        $from = $table->build();
                    } else {
                        $from .= $table->build() . ',';
                    }
                }

                if (is_string($table)) {
                    if ($from !== '') {
                        $from = $table;
                    } else {
                        $from .= $table . ',';
                    }
                }
            }

            if ($from[strlen($from) - 1] == ',') {
                $from = substr($from, 0, strlen($from) - 1);
            }
        }
        return $from;
    }
}