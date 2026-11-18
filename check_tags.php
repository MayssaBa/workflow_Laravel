<?php
$dirs = ['app', 'database', 'routes', 'tests'];
$missing = [];
$bom = [];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) { echo "Dir not found: $dir\n"; continue; }
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->getExtension() !== 'php') continue;
        if ($file->isDir()) continue;
        $path = $file->getPathname();
        $content = file($path);
        if (empty($content)) {
            $missing[] = $path;
            continue;
        }
        $firstLine = trim($content[0]);
        $hasPhpTag = strpos($firstLine, '<?php') === 0;
        $hasBom = substr(trim($firstLine), 0, 3) === "\xef\xbb\xbf";
        if (!$hasPhpTag && !$hasBom) {
            $missing[] = $path;
        } elseif ($hasBom) {
            $bom[] = $path;
        }
    }
}
echo "MISSING <?php tag:\n";
foreach ($missing as $m) echo "  - $m\n";
echo "\nBOM files:\n";
foreach ($bom as $b) echo "  - $b\n";
if (empty($missing) && empty($bom)) {
    echo "All files have <?php tag and no BOM issues.\n";
}