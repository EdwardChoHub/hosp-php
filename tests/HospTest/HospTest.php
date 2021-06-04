<?php


namespace HospTest;


use function hosp\_hosp_resolve;
use function hosp\_hosp_simple_resolve;
use function hosp\_hosp_standard_resolve;
use function hosp\config;
use function hosp\hosp;

class HospTest extends TestCase
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

    public function testHospSimpleResolveInsert()
    {
        $data = _hosp_simple_resolve('insert');
        $this->assertEquals('insert', $data['type']);
    }

    public function testHospSimpleResolveDelete()
    {
        $data = _hosp_simple_resolve('deleteByIdNameNickname');
        $this->assertEquals('delete', $data['type']);
        $this->assertEquals(
            " `id` = '#[id]' AND `name` = '#[name]' AND `nickname` = '#[nickname]'",
            $data['by']
        );
    }

    public function testHospSimpleResolveUpdate()
    {
        $data = _hosp_simple_resolve('updateSetNicknameGradeByIdNickname');
        $this->assertEquals('update', $data['type']);
        $this->assertEquals(" `nickname` = '#[nickname]', `grade` = '#[grade]'", $data['set']);
        $this->assertEquals(" `id` = '#[id]' AND `nickname` = '#[nickname]'", $data['by']);
    }

    public function testHospSimpleResolveSelect()
    {
        $data = _hosp_simple_resolve(
            'selectByIdNameNoneNameGroupGradeUser_idOrderCtime!Dtime'
        );
        $this->assertEquals('select', $data['type']);
        $this->assertEquals(" `id` = '#[id]' AND `name` = '#[name]'", $data['by']);
        $this->assertEquals(' grade, user_id', $data['group']);
        $this->assertArraySubset(['name'], $data['option']['none']);
        $this->assertEquals(' ctime ASC dtime DESC', $data['order']);
    }

    public function testHospStandardResolveInsert()
    {
        $data = _hosp_standard_resolve('insert');
        $this->assertFalse($data);
    }

    public function testHospStandardResolveDelete()
    {
        $data = _hosp_standard_resolve('delete{by[(id&name)|nickname]}');
        $this->assertEquals('delete', $data['type']);
        $this->assertEquals(
            "(`id` = '#[id]' AND `name` = '#[name]') OR `nickname` = '#[nickname]'",
            $data['by']
        );
    }

    public function testHospStandardResolveUpdate()
    {
        $data = _hosp_standard_resolve('delete{by[(id&name)|nickname]set[nickname,grade]}');
        $this->assertEquals(
            "(`id` = '#[id]' AND `name` = '#[name]') OR `nickname` = '#[nickname]'",
            $data['by']
        );
        $this->assertEquals(
            "`nickname` = '#[nickname]',`grade` = '#[grade]'",
            $data['set']
        );

    }

    public function testHospStandardResolveSelect()
    {
        $express = 'selectList{
            by[user_id:user_ids|name]
            order[ctime,!id]
            none[id,nickname]
            only[user_id,nickname]
            group[id,name]
            having[user_id:user_id2&(name:name2|name3)]
            limit[start,limit]
        }';
        $data = _hosp_standard_resolve($express);
        $this->assertEquals(
            "`user_id` = '#[user_ids]' OR `name` = '#[name]'",
            $data['by']
        );
        $this->assertEquals(
            " `ctime` ASC `id` DESC",
            $data['order']
        );
        $this->assertArraySubset(['id','nickname'], $data['none']);
        $this->assertEquals(['user_id', 'nickname'], $data['only']);
        $this->assertEquals('id,name', $data['group']);
        $this->assertEquals(
            "`user_id` = '#[user_id2]' AND `` = '#[]'(`name` = '#[name2]' OR `name3` = '#[name3]')",
            $data['having']
        );
        $this->assertEquals(
            "#[start],#[limit]",
            $data['limit']
        );
    }

    public function testHospResolveInsert(){
        $express = '/user/insert';
        $result = _hosp_resolve($express, ['user' => 'asdf', 'id' => 2, 'ctime' => '123']);
        $this->assertEquals(
            "INSERT INTO `tp_user`(`user`,`id`,`ctime`) values('asdf','2','123')",
            $result[0]
        );
        $this->assertEquals([], $result[1]);

        $express = '/user/insert?user=asdf&id=2&ctime=123';
        $result = _hosp_resolve($express);
        $this->assertEquals(
            "INSERT INTO `tp_user`(`user`,`id`,`ctime`) values('asdf','2','123')",
            $result[0]
        );
        $this->assertEquals([], $result[1]);
    }

    public function testHospResolveDelete()
    {
        $express = "/user/deleteByIdName";
        $result = _hosp_resolve($express, ['id' => '1', 'name' => '2']);
        $this->assertEquals(
            "DELETE FROM `tp_user` WHERE  `id` = '1' AND `name` = '2'",
            $result[0]
        );

        $express = "/user/deleteByIdName?id=1&name=2";
        $result = _hosp_resolve($express);
        $this->assertEquals(
            "DELETE FROM `tp_user` WHERE  `id` = '1' AND `name` = '2'",
            $result[0]
        );
    }

    public function testHospResolveUpdate(){
        $sql = "UPDATE `tp_user` SET  `nickname` = '123' WHERE `id` = '1'";
        $express = '/user/updateByIdSetNickname';
        $get = '?id=1&nickname=123';
        $data = ['id' => 1, 'nickname' => '123'];
        $result = _hosp_resolve($express, $data);
        $this->assertEquals($sql, $result[0]);

        $result = _hosp_resolve($express . $get);
        $this->assertEquals($sql, $result[0]);

        $express = '/user/update{by[id]set[nickname]}';
        $result = _hosp_resolve($express . $get);
        $this->assertEquals(
            "UPDATE `tp_user` SET `nickname` = '123' WHERE 1=1 AND id = '1'"
            ,$result[0]);
    }

    public function testHospResolveSelect(){
        $express = '/user/selectById';
        $express1 = '/user/select{by[id]}';
        $data = ['id' => 1];
        $dataString = http_build_query($data);

        $result = _hosp_resolve($express, $data);
        $result1 = _hosp_resolve($express . $dataString);
        $result2 = _hosp_resolve($express1, $data);
        $result3 = _hosp_resolve($express1 . $dataString);

        $this->assertEquals($result, $result1);
        $this->assertEquals($result2, $result3);

    }

    public function testHosp(){
        $data = ['name' => 'test_hosp' . $this->testId];
        $dataString = http_build_query($data);
        $result = hosp('/test/insert', $data);
        $this->assertEquals(1, $result);

        $result1 = hosp('/test/insert' . $dataString);
        $this->assertEquals(1, $result1);
    }

}
