<?php


namespace HospTest;


use function hosp\_db_auto_timestamp;
use function hosp\_db_complete_insert;
use function hosp\_db_soft_delete;
use function hosp\_db_table_default_values;
use function hosp\_db_table_fields;
use function hosp\config;
use function hosp\db_allow_fields;
use function hosp\db_column;
use function hosp\db_count;
use function hosp\db_delete;
use function hosp\db_exec;
use function hosp\db_field;
use function hosp\db_get;
use function hosp\db_insert;
use function hosp\db_insert_all;
use function hosp\db_select;
use function hosp\db_true_delete;
use function hosp\db_update;
use function hosp\dump;
use function hosp\get_last_sql;
use function hosp\mysql_exec;
use function hosp\mysql_history;

class DbTest extends TestCase
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

    public function testDbTrueDelete()
    {
        db_true_delete('user', true);
        $this->assertTrue(db_true_delete('user'));
    }

    public function testDbSoftDelete()
    {
        config('database.soft_delete', [
            'user.dtime' => 0,
        ]);
        config('database.soft_delete', [
            'user1.dtime' => 0,
        ]);

        $result = _db_soft_delete('user');
        $this->assertEquals(0, $result['dtime']);

    }

    public function testDbAutoTimestamp()
    {
        config('database.auto_event.timestamp', [
            'user1' => [
                'create_time' => 'add_time',
                'update_time' => 'update_time',
            ]
        ]);

        $result = _db_auto_timestamp('user1');
        $this->assertArrayHasKey('update_time', $result);

        $result = _db_auto_timestamp('user1', true);
        $this->assertArrayHasKey('add_time', $result);
        $this->assertArrayHasKey('update_time', $result);

    }

    public function testDbAutoFields()
    {
        $this->assertFalse(db_allow_fields('user1'));

        db_allow_fields('user2', true);
        $this->assertTrue(db_allow_fields('user2'));

        db_allow_fields('user3', false);
        $this->assertFalse(db_allow_fields('user3'));
    }

    public function testDbExec()
    {
        $result = db_exec('SELECT 1+1 as `result`');
        $this->assertEquals(2, $result[0]['result']);
        $this->assertTrue(count(mysql_history()) > 0);
        $this->assertNotNull(get_last_sql());
    }

    public function testDbTableDefaultValues()
    {
        $result = _db_table_default_values('tp_test');
        $this->assertTrue(is_array($result));
        $this->assertTrue($result > 0);
    }

    public function testDbCompleteInsert()
    {
        $data = [
            'name' => '12312',
        ];
        $result = _db_complete_insert('tp_test', $data);
        $this->assertTrue(count($result) > 1);
        $this->assertEquals('12312', $result['name']);
    }

    public function testDbTableFields(){
        $result = _db_table_fields('tp_test');
        $this->assertTrue(count($result) > 0);
    }

    public function testDbDelete()
    {
        $result = db_delete('test', '1=1');
        $this->assertTrue(is_int($result));
        $this->assertNotNull(get_last_sql());
    }

    public function testDbInsert(){
        $result = db_insert('test', [
            'name' => $this->testId
        ]);
        $this->assertEquals(1, $result);
    }

    public function testDbInsertAutoTimestampAndComplete(){
        config('database.auto_event.timestamp', [
            'tp_test' => [
                'create_time' => 'ctime',
                'update_time' => 'mtime',
            ]
        ]);
        config('database.auto_event.complete_insert' , true);

        $result = db_insert('test', [
            'name' => $this->testId
        ]);
        $this->assertEquals(1, $result);
    }

    public function testDbInsertAll(){
        $result = db_insert_all('test', [
            ['name' => '123'],
            ['name' => '12312'],
        ]);
        $this->assertEquals(2, $result);
    }


    public function testDbUpdate(){
        config('database.auto_event.timestamp', [
            'tp_test' => [
                'update_time' => 'mtime',
            ]
        ]);
        $result = db_update('test', [
            'name' => 'test_db_update' . $this->testId
        ], '1=1');
        $this->assertTrue($result > 0);
    }

    public function testDbSelect(){
        $result = db_select(
            'test',
            'ctime = 0',
            'id,name,mtime',
            'mtime DESC',
            '',
            '',
            ''
        );
        $this->assertTrue(is_array($result));
    }

    public function testDbCount(){
        $result = db_count('test', '1=1', 'ctime');
        $this->assertTrue(is_int($result));
    }

    public function testDbColumn(){
        $result = db_column('test', '1=1', 'id');
        $this->assertTrue(is_array($result));
    }

    public function testDbField(){
        $result = db_field('test', 'ctime = 0', 'id');
        $this->assertNotNull($result);
    }

    public function testDbGet(){
        $result = db_get('test', 13);
        $this->assertTrue(is_array($result));
    }

    public function testDbAutoQuery(){

    }

}