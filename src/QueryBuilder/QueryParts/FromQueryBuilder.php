<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\QueryBuilder\QueryParts;

use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilderInterface;

class FromQueryBuilder implements FromPartsBuilderInterface
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
        $subQueryIndex = 1;
        if ($tables instanceof SimpleQueryBuilderInterface) {

            $subQuery = $tables->build();

            $subQuery = sprintf(
                '(%s) as subtable_%s',
                $subQuery,
                $subQueryIndex
            );

            $from = $subQuery;
        }


        if (is_array($tables)) {

            foreach ($tables as $table) {

                if ($table instanceof SimpleQueryBuilderInterface) {

                    $subQuery = $table->build();
                    $subQuery = sprintf(
                        '(%s) as subtable_%s',
                        $subQuery,
                        $subQueryIndex
                    );

                    $subQueryIndex++;
                    if ($from !== '') {
                        $from .= $subQuery;
                    } else {
                        $from = $subQuery . ',';
                    }
                }

                if (is_string($table)) {
                    if ($from == '') {
                        $from = $table . ',';
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