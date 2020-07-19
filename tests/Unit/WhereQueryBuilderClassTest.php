<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\Exception\LogicException;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\FromQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\GroupByQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\SelectQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\QueryParts\WhereQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class InvestmentTest
 */
class WhereQueryBuilderClassTest extends TestCase
{

    private SimpleQueryBuilder $simpleQueryBuilder;

    private SelectQueryBuilder $selectQueryBuilder;
    private FromQueryBuilder $fromQueryBuilder;
    private WhereQueryBuilder $whereQueryBuilder;
    private GroupByQueryBuilder $groupByQueryBuilder;

    protected function setUp(): void
    {
        $this->selectQueryBuilder  = new SelectQueryBuilder();
        $this->fromQueryBuilder    = new FromQueryBuilder();
        $this->whereQueryBuilder   = new WhereQueryBuilder();
        $this->groupByQueryBuilder = new GroupByQueryBuilder();

        $this->simpleQueryBuilder  = new SimpleQueryBuilder(
            $this->selectQueryBuilder,
            $this->fromQueryBuilder,
            $this->whereQueryBuilder,
            $this->groupByQueryBuilder
        );
    }

    /**
     * @test
     */
    public function testWhereWithArrayParameterSuccess(): void
    {
        $select           = ['*', 'author'];
        $from             = ['authors'];
        $conditions       = ['', 'author', '=', 'some author name'];
        $conditionsSecond = ['AND', 'author', '<>', 'another author name'];
        $conditionsThird  = ['AND', 'author', '=', 'test'];

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->where($conditionsThird)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' AND author = 'test'", $query);

    }

    /**
     * @test
     */
    public function testWhereWithStringParameterSuccess(): void
    {
        $select           = '*,author';
        $from             = 'authors';
        $conditions       = "author = 'some author name'";
        $conditionsSecond = "AND author <> 'another author name'";
        $conditionsThird  = "AND author = 'test'";

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->where($conditionsThird)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' AND author = 'test'", $query);

    }

    /**
     * @test
     */
    public function testWhereTypeIsIncorrectParameterException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter WHERE type is not array or is not string');

        $select  = '*';
        $where   = 100500;
        $this->simpleQueryBuilder
            ->select($select)
            ->from('authors')
            ->where($where)
            ->build();
    }

}