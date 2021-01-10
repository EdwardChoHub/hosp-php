<?php

namespace HospTest;

use PHPUnit\Framework\TestCase;
use function hosp\_callback;
use function hosp\_response;
use function hosp\array_to_get_string;
use function hosp\config;
use function hosp\dump;
use function hosp\error;
use function hosp\false;
use function hosp\input;
use function hosp\is_get;
use function hosp\is_post;
use function hosp\session;
use function hosp\success;
use function hosp\true;
use function hosp\url;

/**
 * @author EdwardCho
 * Class UtilTest
 * @package HospTest
 * @backupGlobals disabled
 */
class UtilTest extends TestCase
{
    public function testConfig()
    {
        config('user', 1);
        $this->assertEquals(1, config('user'));

        config('user1', ['id' => 1]);
        $this->assertEquals(1, config('user1.id'));
    }

    public function testSession()
    {
        session('user', 1);
        $this->assertEquals(1, session('user'));
    }

    public function testIsPost()
    {
        $this->assertEquals(true, is_bool(is_post()));
    }

    public function testIsGet()
    {
        $this->assertEquals(true, is_bool(is_get()));
    }

    public function testInput()
    {
        $_GET['id'] = ['id' => 1];
        $this->assertEquals(['id' => 1], input('id'));

        $this->assertEquals(null, input('user'));
    }

    public function testArrayToGetString()
    {
        $this->assertEquals(
            '?a=1&b=2',
            array_to_get_string(['a' => 1, 'b' => 2])
        );
    }

    public function testUrl()
    {
        $this->assertEquals(
            urlencode('/hosp.php/a/b?a=1&b=2'),
            url('/a/b', ['a' => 1, 'b' => 2])
        );
    }

    public function testResponseTypeJson()
    {
        $data = ['a' => 1, 'b' => 2];
        $this->assertEquals(
            json_encode($data, JSON_UNESCAPED_UNICODE),
            _response($data, 'json')[0]
        );
    }

    public function testResponseTypeHtml()
    {
        $html = '123123';
        $this->assertEquals(
            $html,
            _response($html, 'html')[0]
        );
    }

    public function testError()
    {
        $msg = '123';
        $code = -1;
        $expected = json_encode(['msg' => $msg, 'code' => $code], JSON_UNESCAPED_UNICODE);
        $this->assertEquals(
            $expected,
            error($msg, $code)[0]
        );
    }

    public function testSuccess()
    {
        $msg = '123';
        $code = 0;
        $data = ['a' => 1, 'b' => 2];
        $actual = success($data, $code, $msg);

        $this->assertEquals(true, is_string($actual[0]));

        $actual = json_decode($actual[0], true);
        $this->assertEquals(true, is_array($actual));

        $this->assertEquals($msg, $actual['msg']);
        $this->assertEquals($code, $actual['code']);
        $this->assertEquals($data, $actual['data']);
    }

    public function testTrue()
    {
        $data = ['a' => 1, 'b' => 2];
        $result = true($data);
        $this->assertEquals(true, $result[0]);
        $this->assertEquals($data, $result[1]);
    }

    public function testFalse()
    {
        $msg = '123123';
        $result = false($msg);
        $this->assertEquals(false, $result[0]);
        $this->assertEquals($msg, $result[1]);
    }

    public function testCallback()
    {
        config('test.callback', function () {
            return 1;
        });

        $this->assertEquals(1, _callback('test.callback', []));

    }

    public function testInputFilter()
    {
        $params = [
            'a' => htmlspecialchars('<html></html>'),
            'b' => ' b ',
            'c' => htmlspecialchars(' <html></html> '),
        ];
        $this->assertEquals(true, is_array($params));
        $this->assertArrayHasKey('a', $params);
        $this->assertArrayHasKey('b', $params);
        $this->assertArrayHasKey('c', $params);
    }
}
