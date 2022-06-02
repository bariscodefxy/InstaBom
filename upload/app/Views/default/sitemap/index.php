<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $baseUrl = "http" . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
    $this->response->header('Content-Type: application/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>' . $baseUrl . '</loc>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>
    <url>
        <loc>' . $baseUrl . '/tools</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>' . $baseUrl . '/packages</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>' . $baseUrl . '/blog</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>';
    foreach($model AS $blog) {
        echo '<url>
                <loc>' . $baseUrl . '/blog/' . $blog["seoLink"] . '</loc>
                <changefreq>weekly</changefreq>
                <priority>0.6</priority>
              </url>';
    }
    echo '</urlset>';