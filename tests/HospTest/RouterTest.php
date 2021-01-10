<?php


namespace HospTest;


use function hosp\_router;
use function hosp\config;
use function hosp\router;

class RouterTest extends TestCase
{
    public function testRouterRegister()
    {
        $before = '/a/b';
        $after = '/a/c';
        router($before, $after);
        $router = config('router');
        $this->assertEquals($after, $router[$before]);
    }

    public function testRouterNone()
    {
        $controller = 'a';
        $method = 'b';
        config('router', null);
        $router = _router([$controller, $method]);
        $this->assertEquals($controller, $router[0]);
        $this->assertEquals($method, $router[1]);
    }

    public function testRouterHas()
    {
        $controller = 'a';
        $method = 'b';
        config('router', ['/a/b' => '/a/c']);
        $router = _router([$controller, $method]);
        $this->assertEquals($controller, $router[0]);
        $this->assertEquals('c', $router[1]);
    }

    public function testRouterControllerVague()
    {
        $controller = 'a';
        $method = 'b';
        config('router', ['/*/b' => '/a/c']);
        $router = _router([$controller, $method]);
        $this->assertEquals($controller, $router[0]);
        $this->assertEquals('c', $router[1]);
    }

    public function testRouterMethodVague()
    {
        $controller = 'a';
        $method = 'b';
        config('router', ['/a/*' => '/a/c']);
        $router = _router([$controller, $method]);
        $this->assertEquals($controller, $router[0]);
        $this->assertEquals('c', $router[1]);
    }

    public function testRouterVague()
    {
        $controller = 'a';
        $method = 'b';
        config('router', null);
        config('router', ['/a/*' => '/b/*']);
        $router = _router([$controller, $method]);
        $this->assertEquals('b', $router[0]);
        $this->assertEquals($method, $router[1]);
    }

}