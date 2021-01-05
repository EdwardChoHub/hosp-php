<?php


namespace HospTest;


use function hosp\_router;
use function hosp\config;

class TestRouter extends TestCase
{
    public function testRouterNone()
    {
        $controller = 'a';
        $method = 'b';
        config('router', []);
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

    public function testRouterControllerVague(){
        $controller = 'a';
        $method = 'b';
        config('router', ['/*/b' => '/a/c']);
        $router = _router([$controller, $method]);
        $this->assertEquals($controller, $router[0]);
        $this->assertEquals('c', $router[1]);
    }

    public function testRouterMethodVague(){
        $controller = 'a';
        $method = 'b';
        config('router', ['/a/*' => '/a/c']);
        $router = _router([$controller, $method]);
        $this->assertEquals($controller, $router[0]);
        $this->assertEquals('c', $router[1]);
    }

    public function testRouterVague(){
        $controller = 'a';
        $method = 'b';
        config('router', ['/a/*' => '/b/*']);
        $router = _router([$controller, $method]);
        $this->assertEquals('b', $router[0]);
        $this->assertEquals($method, $router[1]);
    }

}