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

    /**
     * @test
     */
    public function checkSelectTypeParameterIsCorrect(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Type of SELECT parameter is incorrect. This can be only array or string');

        $select = 100500;
        $where = "author = 'some author name'";
        $from = 'authors';

        $this->simpleQueryBuilder
            ->select($select)
            ->from($from)
            ->where($where)
            ->build();
    }


    /**
     * @test
     */
    public function buildWithLimit(): void
    {
        $select = 'author';
        $where = "author = 'some author name'";
        $from = 'authors';

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
    public function buildWithLimitIncorrectType(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Type of LIMIT parameter is incorrect. This can be only integer');

        $select = 'author';
        $where = "author = 'some author name'";
        $from = 'authors';

        $this->simpleQueryBuilder
            ->select($select)
            ->limit("test")
            ->from($from)
            ->where($where)
            ->build();
    }

    /**
     * @test
     */
    public function buildWithOffsetIncorrectType(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Type of OFFSET parameter is incorrect. This can be only integer');

        $select = 'author';
        $where = "author = 'some author name'";
        $from = 'authors';

        $this->simpleQueryBuilder
            ->select($select)
            ->offset("test")
            ->from($from)
            ->where($where)
            ->build();
    }

    /**
     * @test
     */
    public function checkSqlStringBuildFromArraysParametersWithBuildCount(): void
    {
        $select = ['author'];
        $from = 'authors';
        $conditions = ['', 'author', '=', 'some author name'];
        $conditionsSecond = ['AND', 'author', '<>', 'another author name'];
        $conditionsThird = ['AND', 'author', '=', 'test'];
        $fieldsGroupBy = ['author'];

        $query = $this->simpleQueryBuilder->select($select);
        $query->buildCount();
        $query = $query
            ->from($from)
            ->where($conditions)
            ->where($conditionsSecond)
            ->where($conditionsThird)
            ->groupBy($fieldsGroupBy)
            ->build();

        $this->assertIsString($query);
        $this->assertEquals("SELECT count(author) FROM authors WHERE author = 'some author name' AND author <> 'another author name' AND author = 'test' GROUP BY author", $query);
        $this->assertNotEquals("SELECT *,author FROM authors WHERE author = 'some author name' AND author <> 'another author name' AND author = 'test' GROUP BY author ", $query);
    }

    /**
     * @test
     */
    public function buildWithSelectIncorrectType(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('SELECT values can be only string or array');
        $this->expectExceptionMessage('Type of SELECT parameter is incorrect. This can be only array or string');

        $select = 100500;
        $where = "author = 'some author name'";
        $from = 'authors';

        $query = $this->simpleQueryBuilder->select($select);
        $query->buildCount();
        $query
            ->from($from)
            ->where($where)
            ->build();
    }

}