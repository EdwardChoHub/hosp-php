<?php


namespace HospTest;


use function hosp\_hosp_simple_resolve;
use function hosp\_hosp_standard_resolve;
use function hosp\dump;

class HospTest extends TestCase
{
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

    }

    public function testHospStandardResolveSelect()
    {

    }

}