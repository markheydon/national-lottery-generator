<?php

declare(strict_types=1);


if (! function_exists('env')) {
    /**
     * Read an environment variable with optional default.
     *
     * @param mixed $default
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $value,
        };
    }
}

if (! function_exists('asset')) {
    /**
     * Generate a URL path for a public asset.
     */
    function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (! function_exists('project_root')) {
    /**
     * Absolute path to the project root directory.
     */
    function project_root(): string
    {
        return dirname(__DIR__);
    }
}

if (! function_exists('storage_path')) {
    /**
     * Absolute path under storage/app/.
     */
    function storage_path(string $relativePath = ''): string
    {
        $base = project_root() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app';

        if ($relativePath === '') {
            return $base;
        }

        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        return $base . DIRECTORY_SEPARATOR . $normalized;
    }
}
