<?php
/**
 * Shabab Setif - Custom Router
 * 
 * Simple and efficient routing system
 * 
 * @package ShababSetif
 * @author Idriss Boukmouche <contact@terminaldz.github.io>
 * @link https://terminaldz.github.io
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Helpers;

class Router
{
    private array $routes = [];
    private string $basePath = '';
    private array $middleware = [];

    /**
     * Set base path for the router
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Add a GET route
     */
    public function get(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Add a POST route
     */
    public function post(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * Add a PUT route
     */
    public function put(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->addRoute('PUT', $path, $handler, $middleware);
    }

    /**
     * Add a DELETE route
     */
    public function delete(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    /**
     * Add route to collection
     */
    private function addRoute(string $method, string $path, callable|array $handler, array $middleware = []): self
    {
        $path = $this->basePath . '/' . ltrim($path, '/');
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
        return $this;
    }

    /**
     * Match and dispatch the current request
     */
    public function dispatch(): mixed
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = $this->basePath . '/' . ltrim($uri, '/');

        // Handle PUT/DELETE via POST with _method field
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Try exact match first
        if (isset($this->routes[$method][$uri])) {
            return $this->executeRoute($this->routes[$method][$uri], []);
        }

        // Try pattern matching for dynamic routes
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $config) {
                $params = $this->matchRoute($route, $uri);
                if ($params !== false) {
                    return $this->executeRoute($config, $params);
                }
            }
        }

        // No route found
        return $this->handleNotFound();
    }

    /**
     * Match route pattern with URI
     */
    private function matchRoute(string $route, string $uri): array|false
    {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }

    /**
     * Execute route handler
     */
    private function executeRoute(array $config, array $params): mixed
    {
        $handler = $config['handler'];
        $middleware = $config['middleware'];

        // Run middleware
        foreach ($middleware as $mw) {
            $result = $this->runMiddleware($mw);
            if ($result !== true) {
                return $result;
            }
        }

        // Execute handler
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            return $controller->$method(...array_values($params));
        }

        return $handler(...array_values($params));
    }

    /**
     * Run middleware
     */
    private function runMiddleware(string|callable $middleware): mixed
    {
        if (is_callable($middleware)) {
            return $middleware();
        }

        if (class_exists($middleware)) {
            $instance = new $middleware();
            return $instance->handle();
        }

        return true;
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void
    {
        http_response_code(404);

        if ($this->isApiRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Route not found'
            ]);
        } else {
            include BASE_PATH . '/views/errors/404.php';
        }
        exit;
    }

    /**
     * Check if request is API request
     */
    private function isApiRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'];
        return str_starts_with($uri, '/api/');
    }

    /**
     * Redirect helper
     */
    public static function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * JSON response helper
     */
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
