<?php


namespace HospTest;

/**
 * @author EdwardCho
 * Class UserTest
 * @package HospTest
 * @backupGlobals disabled
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $testId;
    protected $time;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->testId = random_int(0, 9999);
        $this->time = time();
    }

}