<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): string
    {
        $path = BASE_PATH . '/src/Views/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($path)) {
            throw new \RuntimeException("View not found: $view");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $path;
        $rendered = (string) ob_get_clean();

        if (isset($layout) && is_string($layout)) {
            $content = $rendered;
            ob_start();
            require BASE_PATH . '/src/Views/layouts/' . $layout . '.php';

            return (string) ob_get_clean();
        }

        return $rendered;
    }
}
