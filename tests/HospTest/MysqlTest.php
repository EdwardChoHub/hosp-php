<?php


namespace HospTest;


use function hosp\_mysql;
use function hosp\_mysql_delete;
use function hosp\_mysql_exec;
use function hosp\_mysql_insert;
use function hosp\_mysql_select;
use function hosp\_mysql_update;
use function hosp\config;

class MysqlTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        config('database', [
            'host' => '127.0.0.1',
            'port' => '3306',
            'dbname' => 'test',
            'username' => 'root',
            'password' => 'root',
            //表名前缀
            'table_prefix' => 'tp_',
        ]);

    }

    public function testMysql()
    {
        $mysql = _mysql();
        $this->assertEquals(true, \PDO::class == get_class($mysql));
    }

    public function testMysqlExec()
    {
        $result = _mysql_exec('SELECT 1+1 as `result`');
        $this->assertEquals(2, $result[0]['result']);
    }

    public function testMysqlDelete()
    {
        $result = _mysql_delete('tp_test', '2=1');
        $this->assertTrue(is_int($result));
    }

    public function testMysqlInsert()
    {
        $name = 'test_' . $this->testId;
        $result = _mysql_insert('tp_test', [
            'name' => $name,
            'ctime' => $this->time,
            'mtime' => $this->time,
        ]);
        $this->assertEquals(1, $result);
    }

    public function testMysqlSelect()
    {
        $result = _mysql_select('tp_test', 'name', '1=1', 'name', '', '1', '1=1');
        $this->assertTrue(is_array($result));
    }

    public function testMysqlUpdate()
    {
        $result = _mysql_update('tp_test', ['name' => '测试' . $this->testId], '1=1');
        $this->assertTrue(is_int($result));
    }

}