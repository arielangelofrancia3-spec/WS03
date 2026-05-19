<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router
{
    protected $routes = [];
    protected $routeMap = [];

    public function register($method, $uri, $controller)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller
        ];
    }

    public function __construct(array $routeMap = [])
    {
        $this->routeMap = $routeMap;
    }

    public function registerRoute($method, $uri, $controller, $middleware = [])
    {
        list($controllerName, $controllerMethod) = explode('@', $controller);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controllerName,
            'controllerMethod' => $controllerMethod,
            'middleware' => $middleware
        ];
    }

    public function get($uri, $controller, $middleware = [])
    {
        $this->registerRoute('GET', $uri, $controller, $middleware);
    }

    public function post($uri, $controller, $middleware = [])
    {
        $this->registerRoute('POST', $uri, $controller, $middleware);
    }

    public function put($uri, $controller, $middleware = [])
    {
        $this->registerRoute('PUT', $uri, $controller, $middleware);
    }

    public function delete($uri, $controller, $middleware = [])
    {
        $this->registerRoute('DELETE', $uri, $controller, $middleware);
    }

    public function route($uri)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $requestMethod = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            $uriSegments = explode('/', trim($uri, '/'));
            $routeSegments = explode('/', trim($route['uri'], '/'));

            if (count($uriSegments) !== count($routeSegments) || strtoupper($route['method']) !== $requestMethod) {
                continue;
            }

            $params = [];
            $match = true;

            for ($i = 0; $i < count($uriSegments); $i++) {
                if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                    $match = false;
                    break;
                }

                if (!empty($matches[1])) {
                    $params[$matches[1]] = $uriSegments[$i];
                }
            }

            if (!$match) {
                continue;
            }

            if (!empty($route['middleware'])) {
                foreach ($route['middleware'] as $middleware) {
                    (new Authorize())->handle($middleware);
                }
            }

            $controllerClass = 'App\\Controllers\\' . $route['controller'];
            $controllerMethod = $route['controllerMethod'];

            $controllerInstance = new $controllerClass();
            $controllerInstance->$controllerMethod($params);

            return;
        }

        ErrorController::notFound();
    }
}
