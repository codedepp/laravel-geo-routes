<?php

namespace LaraCrafts\GeoRoutes;

use Closure;

class GeoGroup
{

    /**
     * The router instance
     *
     * @var Illuminate\Routing\Router;
     */
    protected $router;

    /**
     * The group routes
     *
     * @var array
     */
    protected $routes;

    /**
     * The geo constraint
     *
     * @var string
     */
    protected $strategy;

    /**
     * Countries affected by the constraint
     *
     * @var array
     */
    protected $countries;

    /**
     * The callback method
     *
     * @var array
     */
    protected $callback;

    /**
     * Create a new instance of GeoGroup
     *
     * @param array $attributes
     * @param Closure $callback
     *
     * @return $this
     */
    public function __construct(array $attributes, Closure $callback)
    {
        $this->router = app('router');
        $currentRoutes = $this->router->getRoutes()->get();
        $this->router->group($attributes, $callback);
        $allRoutes = $this->router->getRoutes()->get();
        $this->routes = array_udiff($allRoutes, $currentRoutes, "self::getDiff");

        return $this;
    }

    /**
     * Allow access
     *
     * @param string ...$countries
     * @return $this
     */
    public function allowFrom(string ...$countries)
    {
        $this->strategy = 'allow';
        $this->countries = $countries;
        return $this;
    }

    /**
     * Deny access
     *
     * @param string ...$countries
     * @return $this
     */
    public function denyFrom(string ...$countries)
    {
        $this->strategy = 'deny';
        $this->countries = $countries;
        return $this;
    }

    /**
     * Set callback method dynamically
     *
     * @param string $method
     * @param array  $args
     * @return void
     */
    public function __call(string $method, array $args)
    {
        #TODO: Improve this
        $this->callback = [ 'method' => $method, 'args' => $args];
    }

    /**
     * Destruct the GeoGroup instance
     *
     * @return void
     */
    public function __destruct()
    {
        foreach ($this->routes as $route) {
            $GeoRoute = new GeoRoute($route, $this->countries, $this->strategy);
            if (isset($this->callback['method']) && method_exists($GeoRoute, $this->callback['method'])) {
                $GeoRoute->{$this->callback['method']}($this->callback['args']);
            }
        }
    }

    /**
     * Check the difference between objects
     *
     * @param object $obj_1
     * @param object $obj_2
     *
     * @return integer
     */
    protected static function getDiff(object $obj_1, object $obj_2)
    {
        if ($obj_1 === $obj_2) {
            return 0;
        }

        return -1;
    }
}
