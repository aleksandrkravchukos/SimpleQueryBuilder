<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\Exception\LogicException;
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
class FromQueryBuilderClassTest extends TestCase
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
    public function testFromWithStringParameterSuccess(): void
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
    public function testFromWithArrayOfStringParameterSuccess(): void
    {
        $select        = ['*','author'];
        $from          = ['authors', 'books'];
        $conditions    = ['','author', '=', 'some author name'];
        $fieldsGroupBy = ['author'];

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->groupBy($fieldsGroupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors,books WHERE author = 'some author name' GROUP BY author", $query);
        $this->assertNotEquals("test", $query);
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
        $builderSubQuery->from('another_table');
        $builderSubQuery->select('field_from_another_table');


        $select = '*';
        $where  = "author = 'some author name'";
        $query  = $this->simpleQueryBuilder
            ->select($select)
            ->from($builderSubQuery)
            ->where($where)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT * FROM (SELECT field_from_another_table FROM another_table) as subtable_1 WHERE author = 'some author name'", $query);
        $this->assertNotEquals("test", $query);
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
    public function testFromParameterInvalidException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('FROM parameter is incorrect or can not be empty');

        $select = '*';
        $where  = "author = 'some author name'";
        $from   = 100500;

        $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($where)
            ->build();
    }

}