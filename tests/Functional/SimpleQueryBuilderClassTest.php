<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Functional;

use MySimpleQueryBuilder\QueryBuilder\Exception\LogicException;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\FromPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\FromQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\GroupByPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\GroupByQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\HavingPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\HavingQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\OrderByPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\OrderByQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\SelectPartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\SelectQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\WherePartsBuilderInterface;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\WhereQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class SimpleQueryBuilderClassTest
 */
class SimpleQueryBuilderClassTest extends TestCase
{
    private SimpleQueryBuilder $simpleQueryBuilder;

    private SelectPartsBuilderInterface $selectQueryBuilder;
    private FromPartsBuilderInterface $fromQueryBuilder;
    private WherePartsBuilderInterface $whereQueryBuilder;
    private GroupByPartsBuilderInterface $groupByQueryBuilder;
    private OrderByPartsBuilderInterface $orderByQueryBuilder;
    private HavingPartsBuilderInterface $havingQueryBuilder;

    protected function setUp(): void
    {
        $this->selectQueryBuilder  = new SelectQueryBuilder();
        $this->fromQueryBuilder    = new FromQueryBuilder();
        $this->whereQueryBuilder   = new WhereQueryBuilder();
        $this->groupByQueryBuilder = new GroupByQueryBuilder();
        $this->orderByQueryBuilder = new OrderByQueryBuilder();
        $this->havingQueryBuilder  = new HavingQueryBuilder();

        $this->simpleQueryBuilder = new SimpleQueryBuilder(
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
        $conditions       = ['', 'author.name', '=', 'some author name'];
        $conditionsSecond = ['AND', 'author.name', '<>', 'another author name'];
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
        $this->assertEquals("SELECT *,author FROM authors WHERE author.name = 'some author name' AND author.name <> 'another author name' GROUP BY author ORDER BY age LIMIT 10 OFFSET 10", $query);
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
        $havingConditions = ['COUNT','authors.age','>','25'];

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->groupBy($fieldGroupBy)
            ->orderBy($fieldOrderBy)
            ->having($havingConditions)
            ->limit(10)
            ->offset(10)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' GROUP BY author HAVING COUNT(authors.age) > 25 ORDER BY age LIMIT 10 OFFSET 10", $query);
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

    /**
     * @test
     */
    public function testSimpleQueryBuilderBuildCountParameterSuccess(): void
    {
        $select           = ['author'];
        $from             = 'authors';
        $conditions       = ['', 'author.name', '=', 'some author name'];
        $conditionsSecond = ['AND', 'author.name', '<>', 'another author name'];
        $fieldsGroupBy    = ['author'];

        $query = $this->simpleQueryBuilder->select($select);
        $query->buildCount();
        $query = $query
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->groupBy($fieldsGroupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT COUNT(author) as field_count_author FROM authors WHERE author.name = 'some author name' AND author.name <> 'another author name' GROUP BY author", $query);
    }

    /**
     * @test
     */
    public function testSimpleQueryBuilderInvalidSelectParameterException(): void
    {

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Type of SELECT parameter is incorrect. This can be only array or string and can not be empty');

        $from = ['authors'];
        $this->simpleQueryBuilder
            ->from($from)
            ->build();
    }

    /**
     * @test
     */
    public function testSimpleQueryBuilderInvalidFromParameterException(): void
    {

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('FROM parameter is incorrect or can not be empty');

        $select = 'author';
        $this->simpleQueryBuilder
            ->select($select)
            ->build();
    }

    /**
     * @test
     */
    public function testSimpleQueryBuilderInvalidWhereParameterException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter WHERE type is not array or is not string');

        $select = 'author';
        $from   = 'authors';
        $where  = 100500;
        $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($where)
            ->build();
    }

    /**
     * @test
     */
    public function testSimpleQueryBuilderInvalidGroupByParameterException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter GROUP BY type is not array or is not string');

        $select    = 'author';
        $from      = 'authors';
        $condition = "author = 'some author name'";
        $groupBy   = 10500;
        $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($condition)
            ->groupBy($groupBy)
            ->build();
    }

    /**
     * @test
     */
    public function testSimpleQueryBuilderInvalidOrderByParameterException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter ORDER BY type is not array or is not string');

        $select    = 'author';
        $from      = 'authors';
        $condition = "author = 'some author name'";
        $groupBy   = 'author.name';
        $orderBy   = 100500;
        $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($condition)
            ->groupBy($groupBy)
            ->orderBy($orderBy)
            ->build();
    }

    /**
     * @test
     */
    public function testSimpleQueryBuilderInvalidHavingParameterException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter HAVING type is not array or is not string');

        $select    = 'author';
        $from      = 'authors';
        $condition = "author = 'some author name'";
        $groupBy   = 'author.name';
        $orderBy   = 'author.id';
        $having    = 100500;
        $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($condition)
            ->groupBy($groupBy)
            ->orderBy($orderBy)
            ->having($having)
            ->build();
    }

    /**
     * @test
     */
    public function testSimpleQueryBuilderBuildCountInvalidParameterException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('SELECT values can be only string or array and can not be empty');

        $from             = 'authors';
        $conditions       = ['', 'author.name', '=', 'some author name'];
        $conditionsSecond = ['AND', 'author.name', '<>', 'another author name'];
        $fieldsGroupBy    = ['author'];

        $query = $this->simpleQueryBuilder->buildCount();
        $query
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->groupBy($fieldsGroupBy)
            ->build();
    }
}