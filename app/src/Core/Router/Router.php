<?php

namespace App\Core\Router;

class Router
{
    private string $routesFilePath;

    public function __construct(string $routesFilePath)
    {
        $this->routesFilePath = $routesFilePath;
    }

    public function isPathGood()
    {
        return (new \DOMDocument())->load($this->routesFilePath) || yaml_parse_file($this->routesFilePath);
    }

    public function runXML()
    {
        $xml = new \DOMDocument();
        $xml->load($this->routesFilePath);
        $routes = $xml->getElementsByTagName('route');

        $isApiCall = preg_match('/^\/api\/[a-z0-9-]+/', $_SERVER['REQUEST_URI'], $matches);

        $noQuery = explode('?', $_SERVER['REQUEST_URI']);
        $uri = explode('/', $noQuery[0]);

        if ($isApiCall) {
            $path = $matches[0];
            $i = 3;
            //$id = preg_match('/[0-9]+/', $uri[3] ?? '') ? $uri[3] : null;
        } else {
            $path = $uri[1] !== '' ? '/' . $uri[1] : '/';
            $i = 2;
            //$id = preg_match('/^[\d]+$/', $uri[2] ?? '') ? $uri[2] : null;
            //$params = preg_match('/^[a-z0-9-]+$/i', $uri[2] ?? '') ? $uri[2] : null;
        }


        foreach ($routes as $route) {
            if ($route->getAttribute('path') === $path) {
                $controllerClass = 'App\\Controller\\' . $route->getAttribute('controller');
                $action = $route->getAttribute('action');

                $params = [];
                if ($route->hasAttribute('params')) {
                    $keys = explode(',', $route->getAttribute('params'));
                    foreach ($keys as $key) {
                        $key = str_replace(' ', '', $key);
                        $params[$key] = $uri[$i] ?? null;
                        $i++;
                    }
                }

                return new $controllerClass($action, $params, $_SERVER['REQUEST_METHOD']);
            }
        }
    }

    public function runYAML()
    {
        $yaml = yaml_parse_file($this->routesFilePath);

        $isApiCall = preg_match('/^\/api\/[a-z0-9-]+/', $_SERVER['REQUEST_URI'], $matches);

        $noQuery = explode('?', $_SERVER['REQUEST_URI']);
        $uri = explode('/', $noQuery[0]);

        if ($isApiCall) {
            $path = $matches[0];
            $i = 3;
        } else {
            $path = $uri[1] !== '' ? '/' . $uri[1] : '/';
            $i = 2;
        }


        foreach ($yaml as $route) {
            if ($route['path'] === $path) {
                $controllerClass = 'App\\Controller\\' . $route['controller'];
                $action = $route['action'];

                $params = [];
                if (isset($route['params'])) {
                    foreach ($route['params'] as $key) {
                        $key = str_replace(' ', '', $key);
                        $params[$key] = $uri[$i] ?? null;
                        $i++;
                    }
                }

                return new $controllerClass($action, $params, $_SERVER['REQUEST_METHOD']);
            }
        }
    }
}