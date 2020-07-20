<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\QueryParts\GroupByQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class GroupByQueryBuilderClassTest
 */
class GroupByQueryBuilderClassTest extends TestCase
{
    private GroupByQueryBuilder $groupByQueryBuilder;

    protected function setUp(): void
    {
        $this->groupByQueryBuilder = new GroupByQueryBuilder();
    }

    /**
     * @test
     */
    public function testGroupByQueryBuilderWithStringParameterSuccess(): void
    {
        $groupBy = 'author,age';
        $query = $this->groupByQueryBuilder->build($groupBy);

        $this->assertIsString($query);
        $this->assertEquals("author,age", $query);
    }

    /**
     * @test
     */
    public function testGroupByQueryBuilderWithArrayParameterSuccess(): void
    {
        $groupBy = ['author', 'age'];
        $query = $this->groupByQueryBuilder->build($groupBy);

        $this->assertIsString($query);
        $this->assertEquals("author,age", $query);
    }
}