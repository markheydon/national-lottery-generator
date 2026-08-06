<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Plain PHP template renderer.
 */
final class View
{
    /**
     * Render a template with the given data.
     *
     * @param array<string, mixed> $data
     */
    public static function render(string $template, array $data = []): string
    {
        $path = project_root() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . '.php';

        if (! is_file($path)) {
            throw new \RuntimeException('Template not found: ' . $template);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $path;

        return (string) ob_get_clean();
    }
}
