<?php


use function HospTest\config;

class ConfigTest extends TestCase
{
    public function test_config_set(){
        config('user', 1);
        $this->assertEquals(1, config('user'));
    }


}