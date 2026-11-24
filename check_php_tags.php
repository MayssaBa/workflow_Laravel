<?php
$dirs = ['app', 'database', 'routes', 'tests'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->getExtension() !== 'php') continue;
        $path = $file->getPathname();
        $content = file($path);
        $firstLine = trim($content[0] ?? '');
        $hasPhpTag = strpos($firstLine, '<?php') === 0;
        $hasBom = substr(trim($firstLine), 0, 3) === "\xef\xbb\xbf";
        if (!$hasPhpTag && !$hasBom) {
            echo "MISSING: " . $path . PHP_EOL;
        } else if ($hasBom) {
            echo "BOM: " . $path . PHP_EOL;
        }
    }
}