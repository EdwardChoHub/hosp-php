<?php

namespace tests;

use function HospTest\user_id;

final class UserTest extends TestCase
{

    public function testUserId()
    {
        $userId = 123;

        user_id($userId);
        $this->assertEquals($userId, user_id());
    }

}
