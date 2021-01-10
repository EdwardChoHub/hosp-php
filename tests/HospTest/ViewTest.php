<?php


namespace HospTest;


use function hosp\_end;
use function hosp\_response;
use function hosp\action;
use function hosp\assign;
use function hosp\config;
use function hosp\input;
use function hosp\view;

class ViewTest extends TestCase
{
    public function testView(){
        config('view.path', APP_PATH . '/tests/view');

        action('/user/action', function(){
            assign('e', input('e'));
            return view('test');
        });

        $result = action('/user/action', ['e' => 123]);

        $result = _response($result[0], $result[1]);

        _end($result);
    }
}