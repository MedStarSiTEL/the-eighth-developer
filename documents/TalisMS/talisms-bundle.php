#!/usr/bin/env php
<?php
/**
 * bundle.php
 *
 * Usage:
 *   php bundle.php /path/to/talismms/root /path/to/output/dir
 *
 * Produces: /path/to/output/dir/talisman-llm-context.txt
 */

$rootDir   = '/Users/itay/dev-repositories/director_moav/TalisMS';//rtrim($argv[1], DIRECTORY_SEPARATOR);
$outputDir = '/Users/itay/tmp';//rtrim($argv[2], DIRECTORY_SEPARATOR);
$outFile   = $outputDir . DIRECTORY_SEPARATOR . 'talisman-llm-context.txt';

// Validate
if (!is_dir($rootDir)) {
    fwrite(STDERR, "Error: project root '{$rootDir}' is not a directory.\n");
    exit(2);
}
if (!is_dir($outputDir) && !mkdir($outputDir, 0755, true)) {
    fwrite(STDERR, "Error: could not create output directory '{$outputDir}'.\n");
    exit(3);
}
if (file_exists($outFile) && !is_writable($outFile)) {
    fwrite(STDERR, "Error: cannot write to '{$outFile}'.\n");
    exit(4);
}

// Open output file for writing (truncate)
$outHandle = fopen($outFile, 'w');
if (!$outHandle) {
    fwrite(STDERR, "Error: failed to open '{$outFile}' for writing.\n");
    exit(5);
}

// Recursively iterate
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($it as $file) {
    /** @var SplFileInfo $file */
    if (!$file->isFile()) {
        continue;
    }
    $absolutePath = $file->getRealPath();
    // Compute path relative to project root, with forward slashes
    $relative = str_replace('\\', '/', substr($absolutePath, strlen($rootDir) + 1));

    if( strpos($relative,'favicon.ico') !==false ){
	continue;
    }
    
    // Determine tag and attributes
    if (preg_match('#^doors/(.+)$#', $relative, $m)) {
        // e.g. doors/cron.php
        $filename = $file->getBasename('.php');
        $typeAttr = htmlspecialchars($filename, ENT_XML1);
        $tag       = 'TalisDoor';
        $attrs     = sprintf('type="%s" path="%s"', $typeAttr, htmlspecialchars('/' . $relative, ENT_XML1));
    }
    elseif (preg_match('#^(src/|composer\.json$|README\.md$)#', $relative)) {
        $tag   = 'TalisFramework';
        $attrs = sprintf('path="%s"', htmlspecialchars('/' . $relative, ENT_XML1));
    }
    elseif (preg_match('#^(application/|config/|demo/)#', $relative)) {
        $tag   = 'TalisDemoApp';
        $attrs = sprintf('path="%s"', htmlspecialchars('/' . $relative, ENT_XML1));
    }
    else {
        // Skip any extra files you don't want in the context
        continue;
    }

    // Read file contents
    $content = file_get_contents($absolutePath);
    if ($content === false) {
        fwrite(STDERR, "Warning: could not read '{$absolutePath}', skipping.\n");
        continue;
    }

    // Write section
    fwrite($outHandle, "<{$tag} {$attrs}>\n");
    fwrite($outHandle, $content);
    // Ensure ending newline
    if (substr($content, -1) !== "\n") {
        fwrite($outHandle, "\n");
    }
    fwrite($outHandle, "</{$tag}>\n\n");
}

fclose($outHandle);
echo "Bundled LLM context written to {$outFile}\n";

