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
class GroupByQueryBuilderClassTest extends TestCase
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
    public function testGroupByWithArrayParameterSuccess(): void
    {
        $select     = ['*', 'author'];
        $from       = ['authors'];
        $conditions = ['', 'author', '=', 'some author name'];
        $groupBy    = ['author', 'age'];

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->groupBy($groupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' GROUP BY author,age", $query);

    }

    /**
     * @test
     */
    public function testGroupByWithStringParameterSuccess(): void
    {
        $select     = ['*', 'author'];
        $from       = ['authors'];
        $conditions = ['', 'author', '=', 'some author name'];
        $groupBy    = 'author,age';

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->groupBy($groupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' GROUP BY author,age", $query);

    }

    /**
     * @test
     */
    public function testGroupByTypeIsIncorrectParameterException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter GROUP BY type is not array or is not string');

        $conditions = ['', 'author', '=', 'some author name'];
        $select     = ['*', 'author'];
        $groupBy    = 100500;
        $from       = ['authors'];
        $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->groupBy($groupBy)
            ->build();
    }

}