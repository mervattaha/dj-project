<?php

class Router {
    private $routes = [];
    private $twig;
    private $pdo;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->pdo = $pdo;
    }

    public function add($route, $handler) {
        // دعم الطرق الديناميكية
        $this->routes[$route] = $handler;
    }

    public function dispatch($url) {
        foreach ($this->routes as $route => $handler) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $route);
            $pattern = str_replace('/', '\/', $pattern);
            $pattern = sprintf('/^%s$/', $pattern);
            
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches);
                call_user_func_array($handler, $matches);
                return;
            }
        }
        
        // إذا لم يتم العثور على مسار مطابق
        $this->log404($url);
        $this->handle404();
    }

    private function handle404() {
        // عرض صفحة خطأ مخصصة
        echo $this->twig->render('404.twig', ['message' => 'Page not found']);
    }

    private function log404($url) {
        // تسجيل أخطاء 404
        error_log("404 Not Found: $url");
    }
}
