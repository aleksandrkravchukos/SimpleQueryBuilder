<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\QueryParts\OrderByQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class InvestmentTest
 */
class OrderByQueryBuilderClassTest extends TestCase
{

    private OrderByQueryBuilder $orderByQueryBuilder;

    protected function setUp(): void
    {
        $this->orderByQueryBuilder = new OrderByQueryBuilder();
    }

    /**
     * @test
     */
    public function testOrderByQueryBuilderWithArrayParameterSuccess(): void
    {
        $orderBy = ['author', 'age'];

        $query = $this->orderByQueryBuilder->build($orderBy);

        $this->assertIsString($query);
        $this->assertEquals("author,age", $query);
    }

    /**
     * @test
     */
    public function testOrderByQueryBuilderWithStringParameterSuccess(): void
    {
        $orderBy    = 'author,age';

        $query = $this->orderByQueryBuilder->build($orderBy);

        $this->assertIsString($query);
        $this->assertEquals("author,age", $query);
    }
}