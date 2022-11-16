<?php

use Illuminate\Support\Str;

if (!function_exists('is_assoc_array')) {
    /**
     * @return boolean
     */
    function is_assoc_array(array $var) {
        return count(
            array_filter(array_keys($var), 'is_string')
        ) > 0;
    }
}

if (!function_exists('nsval')) {
    /**
     * String to namespace
     *
     * @return string
     */
    function nsval(string $value) {
        return preg_replace_callback(
            '/(?:(?:^|\.|\s|\\\{1,2})([A-z\_]*))/',
            function ($match) {
                $match[1] = Str::studly($match[1]);
                $match[1] = "\\{$match[1]}";
                return $match[1];
            },
            $value
        );
    }
}

if (!function_exists('ns_search')) {
    /**
     * Seach the same class in another namspace
     *
     * @return string|null
     */
    function ns_search(string $namespace, string $target, $replaces = []) {
        $target = Str::studly($target);

        foreach ($replaces as $origin => $value) {
            $namespace = Str::replace($origin, $value, $namespace);
        }

        preg_match('/(?:[A-Z][a-z]*?)$/', $namespace, $origin);
        $origin = $origin[0];

        if (!Str::contains($namespace, Str::plural($origin))) {
            $origin = 'Model';
        }

        $namespace = Str::replace(
            Str::plural($origin), Str::plural($target), $namespace
        );
        $namespace = Str::replace($origin, $target, $namespace);

        if (!class_exists($namespace)) {
            $namespace = Str::replaceLast($target, '', $namespace);
        }

        return class_exists($namespace)? $namespace: null;
    }
}
