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
class BuildCountQueryClassTest extends TestCase
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
    public function testBuildCountFromArraysParametersSuccess(): void
    {
        $select           = ['author'];
        $from             = ['authors'];
        $conditions       = ['', 'age', '=', 18];

        $query = $this->simpleQueryBuilder->select($select);
        $query->buildCount();

        $query = $query
            ->from($from)
            ->where($conditions)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT count(author) FROM authors WHERE age = '18'", $query);
    }

    /**
     * @test
     */
    public function testBuildCountWithoutSelectParametersException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('SELECT values can be only string or array and can not be empty');

        $from             = ['authors'];
        $conditions       = ['', 'age', '=', 18];

        $query = $this->simpleQueryBuilder->buildCount();

        $query = $query
            ->from($from)
            ->where($conditions)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT count(author) FROM authors WHERE age = '18'", $query);
    }

}