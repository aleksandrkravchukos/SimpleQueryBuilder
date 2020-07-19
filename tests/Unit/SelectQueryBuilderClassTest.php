<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\Exception\LogicException;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\FromQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\GroupByQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\HavingQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\SelectQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\WhereQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class InvestmentTest
 */
class SelectQueryBuilderClassTest extends TestCase
{
    private SimpleQueryBuilder $simpleQueryBuilder;

    private SelectQueryBuilder $selectQueryBuilder;
    private FromQueryBuilder $fromQueryBuilder;
    private WhereQueryBuilder $whereQueryBuilder;
    private GroupByQueryBuilder $groupByQueryBuilder;
    private HavingQueryBuilder $havingQueryBuilder;

    protected function setUp(): void
    {
        $this->selectQueryBuilder  = new SelectQueryBuilder();
        $this->fromQueryBuilder    = new FromQueryBuilder();
        $this->whereQueryBuilder   = new WhereQueryBuilder();
        $this->groupByQueryBuilder = new GroupByQueryBuilder();
        $this->havingQueryBuilder = new HavingQueryBuilder();

        $this->simpleQueryBuilder  = new SimpleQueryBuilder(
            $this->selectQueryBuilder,
            $this->fromQueryBuilder,
            $this->whereQueryBuilder,
            $this->groupByQueryBuilder,
            $this->havingQueryBuilder
        );
    }

    /**
     * @test
     */
    public function testSelectWithStringParameterSuccess(): void
    {
        $select        = '*,author';
        $from          = 'authors';
        $conditions    = "author = 'some author name'";
        $fieldsGroupBy = 'author';

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->groupBy($fieldsGroupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' GROUP BY author", $query);
        $this->assertNotEquals("test", $query);
    }

    /**
     * @test
     */
    public function testSelectWithArrayParameterSuccess(): void
    {
        $select           = ['*', 'author'];
        $from             = ['authors'];
        $conditions       = ['', 'author', '=', 'some author name'];
        $conditionsSecond = ['AND', 'author', '<>', 'another author name'];
        $fieldsGroupBy    = ['author'];

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->groupBy($fieldsGroupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' GROUP BY author", $query);

    }

    /**
     * @test
     */
    public function testSelectIfNotSetException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Type of SELECT parameter is incorrect. This can be only array or string and can not be empty');

        $this->simpleQueryBuilder
            ->from('authors')
            ->build();
    }

}