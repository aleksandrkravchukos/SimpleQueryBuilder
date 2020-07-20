<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Functional;

use MySimpleQueryBuilder\QueryBuilder\QueryParts\FromQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\GroupByQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\HavingQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\OrderByQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\SelectQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\WhereQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class InvestmentTest
 */
class SimpleQueryBuilderClassTest extends TestCase
{
    private SimpleQueryBuilder $simpleQueryBuilder;

    private SelectQueryBuilder $selectQueryBuilder;
    private FromQueryBuilder $fromQueryBuilder;
    private WhereQueryBuilder $whereQueryBuilder;
    private GroupByQueryBuilder $groupByQueryBuilder;
    private OrderByQueryBuilder $orderByQueryBuilder;
    private HavingQueryBuilder $havingQueryBuilder;

    protected function setUp(): void
    {
        $this->selectQueryBuilder  = new SelectQueryBuilder();
        $this->fromQueryBuilder    = new FromQueryBuilder();
        $this->whereQueryBuilder   = new WhereQueryBuilder();
        $this->groupByQueryBuilder = new GroupByQueryBuilder();
        $this->orderByQueryBuilder = new OrderByQueryBuilder();
        $this->havingQueryBuilder  = new HavingQueryBuilder();

        $this->simpleQueryBuilder  = new SimpleQueryBuilder(
            $this->selectQueryBuilder,
            $this->fromQueryBuilder,
            $this->whereQueryBuilder,
            $this->groupByQueryBuilder,
            $this->orderByQueryBuilder,
            $this->havingQueryBuilder
        );
    }

    /**
     * @test
     */
    public function testSimpleQueryBuilderFromArraysParameters(): void
    {
        $select           = ['*', 'author'];
        $from             = ['authors'];
        $conditions       = ['', 'author', '=', 'some author name'];
        $conditionsSecond = ['AND', 'author', '<>', 'another author name'];
        $fieldGroupBy     = ['author'];
        $fieldOrderBy     = ['age'];

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->groupBy($fieldGroupBy)
            ->orderBy($fieldOrderBy)
            ->limit(10)
            ->offset(10)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' GROUP BY author ORDER BY age LIMIT 10 OFFSET 10", $query);
    }

    /**
     * @test
     */
    public function testSimpleQueryBuilderFromStringsParameters(): void
    {
        $select           = '*,author';
        $from             = 'authors';
        $conditions       = "author = 'some author name'";
        $conditionsSecond = "AND author <> 'another author name'";
        $fieldGroupBy     = 'author';
        $fieldOrderBy     = 'age';

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->groupBy($fieldGroupBy)
            ->orderBy($fieldOrderBy)
            ->limit(10)
            ->offset(10)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' GROUP BY author ORDER BY age LIMIT 10 OFFSET 10", $query);
    }


    /**
     * @test
     */
    public function testFromWithArrayOfSimpleQueryBuilderInterfaceParameterSuccess(): void
    {
        $builderSubQuery1 = new SimpleQueryBuilder(
            $this->selectQueryBuilder,
            $this->fromQueryBuilder,
            $this->whereQueryBuilder,
            $this->groupByQueryBuilder,
            $this->orderByQueryBuilder,
            $this->havingQueryBuilder
        );
        $builderSubQuery1->from(['authors']);
        $builderSubQuery1->select('authors_another_table');

        $builderSubQuery2 = new SimpleQueryBuilder(
            $this->selectQueryBuilder,
            $this->fromQueryBuilder,
            $this->whereQueryBuilder,
            $this->groupByQueryBuilder,
            $this->orderByQueryBuilder,
            $this->havingQueryBuilder
        );
        $builderSubQuery2->from(['authors2']);
        $builderSubQuery2->select('authors_another_table2');

        $select = '*';
        $where  = "author = 'some author name'";

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from([$builderSubQuery1, $builderSubQuery2])
            ->where($where)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT * FROM (SELECT authors_another_table FROM authors) as subtable_1,(SELECT authors_another_table2 FROM authors2) as subtable_2 WHERE author = 'some author name'", $query);
    }

    /**
     * @test
     */
    public function testFromWithSimpleQueryBuilderInterfaceParameterSuccess(): void
    {
        $builderSubQuery = new SimpleQueryBuilder(
            $this->selectQueryBuilder,
            $this->fromQueryBuilder,
            $this->whereQueryBuilder,
            $this->groupByQueryBuilder,
            $this->orderByQueryBuilder,
            $this->havingQueryBuilder
        );
        $builderSubQuery->from(['authors']);
        $builderSubQuery->select('authors_another_table');

        $select = '*';
        $where  = "author = 'some author name'";

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($builderSubQuery)
            ->where($where)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT * FROM (SELECT authors_another_table FROM authors) as subtable_1 WHERE author = 'some author name'", $query);
    }

}