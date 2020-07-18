<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilderInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class InvestmentTest
 */
class SimpleQueryBuilderClassTest extends TestCase
{

    private SimpleQueryBuilderInterface $simpleQueryBuilder;

    protected function setUp(): void
    {
        $this->simpleQueryBuilder = new SimpleQueryBuilder();
    }

    /**
     * @test
     */
    public function checkSqlStringBuild(): void
    {
        $conditions = ['', 'author', '=', 'some author name'];
        $conditions2 = ['AND', 'author', '<>', 'another author name'];
        $conditions3 = ["AND author = 'test'"];
        $fieldsGroupBy = ['author'];

        $select = $this->simpleQueryBuilder
            ->select(['*', 'author'])
            ->from(['authors'])
            ->where($conditions)
            ->where($conditions2)
            ->where($conditions3)
            ->groupBy($fieldsGroupBy)
            ->build();

        $this->assertIsString($select);
        $this->assertEquals("SELECT *,author FROM authors  WHERE   author = 'some author name'  AND author <> 'another author name'   GROUP BY author ", $select);

    }
}