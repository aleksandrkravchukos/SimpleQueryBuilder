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
        $from = 'authors';
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
        $from = 'authors';
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

        $this->simpleQueryBuilder
            ->from('authors')
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

    /**
     * @test
     */
    public function checkWhereTypeParameterException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter WHERE type is not array or is not string');

        $from = '*';
        $where = 100500;
        $this->simpleQueryBuilder
            ->select($from)
            ->from('authors')
            ->where($where)
            ->build();
    }

    /**
     * @test
     */
    public function checkFromTypeParameterIsSimpleQueryBuilderInterface(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter WHERE type is not array or is not string');

        $select = '*';
        $where = 100500;
        $builder = new SimpleQueryBuilder();
        $builder->from(['authors']);
        $builder->select('authors_another');

        $this->simpleQueryBuilder
            ->select($select)
            ->from($builder)
            ->where($where)
            ->build();
    }

    /**
     * @test
     */
    public function checkFromTypeParameterIsSimpleQueryBuilderInterfaceArray(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter WHERE type is not array or is not string');

        $select = '*';
        $where = 100500;
        $builder = new SimpleQueryBuilder();
        $builder->from(['authors']);
        $builder->select('authors_another');

        $this->simpleQueryBuilder
            ->select($select)
            ->from([$builder])
            ->where($where)
            ->build();
    }

    /**
     * @test
     */
    public function checkFromTypeParameterIsInteger(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The parameter WHERE type is not array or is not string');
        $this->expectExceptionMessage('Type of parameters FROM is incorrect');

        $select = '*';
        $where = "author = 'some author name'";
        $from = 100500;

        $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($where)
            ->build();
    }
}