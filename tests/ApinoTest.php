<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
require_once '../apino.php';

final class ApinoTest extends TestCase
{
    public function testSimpleResolver():void{
        $this->assertEquals(
            "SELECT * FROM `tp_user` WHERE 1=1 AND `user_id` = '1'",
            ApinoSql::build('tp_user','selectListByUser_id', ['user_id' => 1])->sql()
        );

        $this->assertEquals(
            "DELETE FROM `tp_user` WHERE 1=1 AND `id` = '1'",
            ApinoSql::build('tp_user', 'deleteById', ['id' => 1])->sql()
        );

        $this->assertEquals(
            "UPDATE `tp_user` SET `name` = '2' WHERE 1=1 AND `id` = '1'",
            ApinoSql::build('tp_user', 'updateByIdSetName', ['id' => 1, 'name' => 2])->sql()
        );
    }

}