<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\QueryParts\HavingQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class HavingQueryBuilderClassTest
 */
class HavingQueryBuilderClassTest extends TestCase
{

    private HavingQueryBuilder $havingQueryBuilder;

    protected function setUp(): void
    {
        $this->havingQueryBuilder = new HavingQueryBuilder();
    }

    /**
     * @test
     */
    public function testFromQueryBuilderWithStringParameterSuccess(): void
    {
        $conditions = "'COUNT(authors.age) > '25'";
        $query = $this->havingQueryBuilder->build($conditions);

        $this->assertIsString($query);
        $this->assertEquals(" 'COUNT(authors.age) > '25' ", $query);
    }

    /**
     * @test
     */
    public function testFromQueryBuilderWithArrayParameterSuccess(): void
    {
        $conditions = ['COUNT','authors.age','>','25'];
        $query = $this->havingQueryBuilder->build($conditions);

        $this->assertIsString($query);
        $this->assertEquals(" COUNT(authors.age) > 25 ", $query);
    }
}