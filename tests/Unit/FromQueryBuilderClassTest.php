<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\QueryParts\FromQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilderInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class InvestmentTest
 */
class FromQueryBuilderClassTest extends TestCase
{

    private SimpleQueryBuilderInterface $simpleQueryBuilderMock;
    private SimpleQueryBuilderInterface $simpleQueryBuilderMockAnother;

    private FromQueryBuilder $fromQueryBuilder;

    protected function setUp(): void
    {
        $this->fromQueryBuilder = new FromQueryBuilder();

        $this->simpleQueryBuilderMock = $this->createMock(SimpleQueryBuilder::class);
        $this->simpleQueryBuilderMockAnother = $this->createMock(SimpleQueryBuilder::class);
    }

    /**
     * @test
     */
    public function testFromQueryBuilderWithStringParameterSuccess(): void
    {
        $from = 'authors,another_table';
        $query = $this->fromQueryBuilder->build($from);
        $this->assertIsString($query);
        $this->assertEquals("authors,another_table", $query);
    }

    /**
     * @test
     */
    public function testFromQueryBuilderWithArrayParameterSuccess(): void
    {
        $from = ['authors', 'another_table'];
        $query = $this->fromQueryBuilder->build($from);
        $this->assertIsString($query);
        $this->assertEquals("authors,another_table", $query);
    }

    /**
     * @test
     */
    public function testFromSimpleQueryBuilderInterfaceParameterSuccess(): void
    {
        $this->simpleQueryBuilderMock->expects(self::once())
            ->method('build')
            ->willReturn('SELECT field_from_another_table FROM another_table');

        $query = $this->fromQueryBuilder->build($this->simpleQueryBuilderMock);

        $this->assertEquals("(SELECT field_from_another_table FROM another_table) as subtable_1", $query);
    }


    /**
     * @test
     */
    public function testFromArrayOfSimpleQueryBuilderInterfaceParameterSuccess(): void
    {
        $this->simpleQueryBuilderMock->expects(self::once())
            ->method('build')
            ->willReturn('SELECT field_from_another_table FROM another_table');

        $this->simpleQueryBuilderMockAnother->expects(self::once())
            ->method('build')
            ->willReturn('SELECT field_from_another_table2 FROM another_table2');

        $query = $this->fromQueryBuilder->build([$this->simpleQueryBuilderMock, $this->simpleQueryBuilderMockAnother]);

        $this->assertEquals("(SELECT field_from_another_table FROM another_table) as subtable_1,(SELECT field_from_another_table2 FROM another_table2) as subtable_2", $query);
    }
}