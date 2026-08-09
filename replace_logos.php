<?php
$directory = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        
        // Replace in img tags or everywhere? 
        // The user said "entire website". I will replace asset('images/chandla-logo.png') and asset('images/chandla-favicon.png') 
        // with asset('images/logo.jpeg') for all image references.
        
        // Specifically replacing the exact asset calls:
        $content = str_replace("asset('images/chandla-logo.png')", "asset('images/logo.jpeg')", $content);
        $content = str_replace('asset("images/chandla-logo.png")', 'asset("images/logo.jpeg")', $content);
        
        // Replace favicon in img src
        $content = str_replace("src=\"{{ asset('images/chandla-favicon.png') }}\"", "src=\"{{ asset('images/logo.jpeg') }}\"", $content);
        $content = str_replace("src=\"{{ file_exists(public_path('images/chandla-favicon.png')) ? asset('images/chandla-favicon.png') : asset('images/chandla-favicon.png') }}\"", "src=\"{{ asset('images/logo.jpeg') }}\"", $content);

        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Done.\n";
