<?php declare(strict_types=1);

namespace MySimpleQueryBuilder\Unit;

use MySimpleQueryBuilder\QueryBuilder\Exception\LogicException;
use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilder;
use MySimpleQueryBuilder\QueryBuilder\SimpleQueryBuilderInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class InvestmentTest
 */
class SimpleQueryBuilderClassTest extends TestCase
{

    private SimpleQueryBuilderInterface $simpleQueryBuilder;

    protected function setUp(): void
    {
        $this->simpleQueryBuilder = new SimpleQueryBuilder();
    }

    /**
     * @test
     */
    public function checkSqlStringBuildFromArraysParameters(): void
    {
        $select = ['*', 'author'];
        $from = ['authors'];
        $conditions = ['', 'author', '=', 'some author name'];
        $conditionsSecond = ['AND', 'author', '<>', 'another author name'];
        $conditionsThird = ['AND', 'author', '=', 'test'];
        $fieldsGroupBy = ['author'];

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->where($conditionsThird)
            ->groupBy($fieldsGroupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' AND author = 'test' GROUP BY author", $query);
        $this->assertNotEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' AND author = 'test' GROUP BY author ", $query);
    }

    /**
     * @test
     */
    public function checkSqlStringBuildFromArraysParametersWithOneCondition(): void
    {
        $select = ['*', 'author'];
        $from = ['authors'];
        $conditions = ['', 'author', '=', 'some author name'];
        $fieldsGroupBy = ['author'];

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->groupBy($fieldsGroupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' GROUP BY author", $query);
        $this->assertNotEquals(" test", $query);
    }

    /**
     * @test
     */
    public function checkSqlStringBuildFromStringsParameters(): void
    {
        $select = '*,author';
        $from = 'authors';
        $conditions = "author = 'some author name'";
        $conditionsSecond = "AND author <> 'another author name'";
        $conditionsThird = "AND author = 'test'";
        $fieldsGroupBy = 'author';

        $query = $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->where($conditionsThird)
            ->groupBy($fieldsGroupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' AND author = 'test' GROUP BY author", $query);
    }

    /**
     * @test
     */
    public function checkFilledQuerySelectParameter(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter SELECT is not filled');

        $from = 'authors';
        $this->simpleQueryBuilder
            ->from($from)
            ->build();
    }

    /**
     * @test
     */
    public function checkFilledQueryFromParameterException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter FROM is not filled');

        $from = '*';
        $this->simpleQueryBuilder
            ->select($from)
            ->build();
    }
}