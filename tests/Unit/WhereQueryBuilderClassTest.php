<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\QueryParts\WhereQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class InvestmentTest
 */
class WhereQueryBuilderClassTest extends TestCase
{
    private WhereQueryBuilder $whereQueryBuilder;

    protected function setUp(): void
    {
        $this->whereQueryBuilder   = new WhereQueryBuilder();
    }

    /**
     * @test
     */
    public function testWhereQueryBuilderWithArrayParameterSuccess(): void
    {
        $conditions    = ['AND','author', '=', 'some author name'];;

        $query = $this->whereQueryBuilder->build($conditions);

        $this->assertIsString($query);
        $this->assertEquals("AND author = 'some author name' ", $query);
    }

    /**
     * @test
     */
    public function testWhereQueryBuilderWithStringParameterSuccess(): void
    {
        $conditions    = "author = 'some author name'";

        $query = $this->whereQueryBuilder->build($conditions);

        $this->assertIsString($query);
        $this->assertEquals("author = 'some author name' ", $query);
    }
}