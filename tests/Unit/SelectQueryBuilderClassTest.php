<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\QueryParts\SelectQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class InvestmentTest
 */
class SelectQueryBuilderClassTest extends TestCase
{
    private SelectQueryBuilder $selectQueryBuilder;

    protected function setUp(): void
    {
        $this->selectQueryBuilder = new SelectQueryBuilder();
    }

    /**
     * @test
     */
    public function testOrderByQueryBuilderWithArrayParameterSuccess(): void
    {
        $orderBy    = ['author', 'age'];

        $query = $this->selectQueryBuilder->build($orderBy);

        $this->assertIsString($query);
        $this->assertEquals("author,age", $query);
    }

    /**
     * @test
     */
    public function testOrderByQueryBuilderWithStringParameterSuccess(): void
    {
        $orderBy    = 'author,age';

        $query = $this->selectQueryBuilder->build($orderBy);

        $this->assertIsString($query);
        $this->assertEquals("author,age", $query);
    }
}