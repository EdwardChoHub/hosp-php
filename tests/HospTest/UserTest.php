<?php

namespace HospTest;


use function hosp\_user_access;
use function hosp\_user_authority;
use function hosp\user_id;
use function hosp\user_login;
use function hosp\user_logout;
use function hosp\user_role;


/**
 * @author EdwardCho
 * Class UserTest
 * @package HospTest
 * @backupGlobals disabled
 */
final class UserTest extends TestCase
{

    public function testUserId()
    {
        $userId = 123;
        user_id($userId);
        $this->assertEquals($userId, user_id());
    }

    public function testUserRole()
    {
        $userRole = '测试';
        user_role($userRole);
        $this->assertEquals($userRole, user_role());
    }

    public function testUserAuthority()
    {
        $data = ['a' => 1];
        _user_authority($data);
        $this->assertEquals($data, _user_authority());

        $data = false;
        _user_authority($data);
        $this->assertEquals($data, _user_authority());

        $data = true;
        _user_authority($data);
        $this->assertEquals($data, _user_authority());

        $data = null;
        _user_authority($data);
        $this->assertEquals($data, _user_authority());
    }

    public function testUserLogin()
    {
        $userId = 123;
        $userRole = '测试';
        user_login($userId, $userRole);

        $this->assertEquals(123, user_id());
        $this->assertEquals($userRole, user_role());
    }

    public function testUserLogout()
    {
        $userId = 123;
        $userRole = '测试';
        user_login($userId, $userRole);
        user_logout();

        $this->assertEquals(null, user_id());
        $this->assertEquals(null, user_role());
        $this->assertEquals(null, _user_authority());
    }

    public function testUserAccess()
    {
        $url = '/user/info';
        _user_authority([$url]);
        $this->assertEquals(true, _user_access($url));


        _user_authority(true);
        $this->assertEquals(true, _user_access($url));

        _user_authority(false);
        $this->assertEquals(false, _user_access($url));

        _user_authority([]);
        $this->assertEquals(false, _user_access($url));

    }
}
