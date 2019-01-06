<?php

namespace LaraCrafts\GeoRoutes\Tests\Unit;

use Illuminate\Http\Request;
use LaraCrafts\GeoRoutes\Http\Middleware\GeoRoutesMiddleware;
use LaraCrafts\GeoRoutes\Tests\TestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class GeoRoutesMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var \LaraCrafts\GeoRoutes\Http\Middleware\GeoRoutesMiddleware */
    protected $middleware;

    /** @var \Closure */
    protected $next;

    /** @var \Illuminate\Http\Request */
    protected $request;

    protected function setUp()
    {
        parent::setUp();

        $this->middleware = new GeoRoutesMiddleware();
        $this->next = function () {
            return 'User got through';
        };
        $this->request = new Request(['country' => 'us']);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     * @small
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function denyDeniessDeniedCountry()
    {
        $this->middleware->handle($this->request, $this->next, 'deny', 'us');
    }

    /**
     * @test
     * @small
     */
    public function MiddlewareAllowsAccess()
    {
        $output = $this->middleware->handle($this->request, $this->next, 'allow', 'us');
        $this->assertEquals('User got through', $output);
    }
    
    /**
     * @test
     * @small
     */
    public function MiddlewareExecutesCallback()
    {
        $mockClass = Mockery::mock('alias:mockClass');
        $mockClass->shouldReceive('callback')
                  ->once()
                  ->with('arg')
                  ->andReturn('MockCallback');

        $callback = serialize(['mockClass::callback', ['arg']]);

        $output = $this->middleware->handle(new Request(['country' => 'es']), $this->next, 'allow', 'us', $callback);
        
        $this->assertEquals('MockCallback', $output);
    }
}
