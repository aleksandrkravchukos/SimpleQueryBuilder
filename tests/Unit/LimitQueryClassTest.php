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
class LimitQueryClassTest extends TestCase
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
    public function testLimitSuccess(): void
    {
        $select = 'author';
        $where  = "author = 'some author name'";
        $from   = 'authors';

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->limit(10)
            ->from($from)
            ->where($where)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT author FROM authors WHERE author = 'some author name' LIMIT 10", $query);
    }

    /**
     * @test
     */
    public function testLimitIncorrectTypeException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Limit can be only integer and more than 0');

        $select = 'author';
        $where  = "author = 'some author name'";
        $from   = 'authors';

        $this->simpleQueryBuilder
            ->select($select)
            ->limit("test")
            ->from($from)
            ->where($where)
            ->build();
    }


}