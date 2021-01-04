<?php

namespace HospTest;

/**
 * 测试基类（引入依赖）
 * @author EdwardCho
 * Class TestCase
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        require_once './hosp.php';
    }
}