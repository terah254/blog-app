<?php

if (!function_exists('clean')) {
    /**
     * Sanitize HTML content by removing dangerous tags and attributes.
     */
    function clean(string $html): string
    {
        $allowedTags = '<p><br><strong><b><em><i><u><strike><del><sub><sup>' .
                       '<h1><h2><h3><h4><h5><h6>' .
                       '<ul><ol><li>' .
                       '<blockquote><pre><code>' .
                       '<a><img>' .
                       '<div><span><table><thead><tbody><tr><td><th>' .
                       '<iframe><figure><figcaption>';

        $html = strip_tags($html, $allowedTags);

        $html = preg_replace('/\s*on\w+="[^"]*"/i', '', $html);
        $html = preg_replace('/\s*on\w+=\'[^\']*\'/i', '', $html);
        $html = preg_replace('/javascript\s*:/i', '', $html);
        $html = preg_replace('/src\s*=\s*"data:(?!image\/)[^"]*"/i', '', $html);

        return $html;
    }
}

if (!function_exists('str_limit_clean')) {
    /**
     * Limit string length and strip HTML tags.
     */
    function str_limit_clean(string $text, int $limit = 100): string
    {
        $text = strip_tags($text);
        return Str::limit($text, $limit);
    }
}
