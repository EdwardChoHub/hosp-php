<?php


namespace HospTest;


use function hosp\verify_code_check;
use function hosp\verify_code_save;

class VerifyCodeTest extends TestCase
{
    public function testVerifyCodeCheck()
    {
        $code = '1234';
        verify_code_save($code);
        $this->assertEquals(false, verify_code_check('123')[0]);

        $this->assertEquals(true, verify_code_check($code)[0]);
    }

}