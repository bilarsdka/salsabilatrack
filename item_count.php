<?php
$html = file_get_contents('_rendered2.html');
$lines = explode("\n", $html);

// Count total MENU_DATA items
echo "=== Makanan items ===\n<br>
";
$counts = ['makanan' => 0, 'minuman' => 0, 'tambahan' => 0];
$current = null;
foreach ($lines as $i => $line) {
    if (preg_match('/makanan:\s*\[/', $line)) $current = 'makanan';
    if (preg_match('/minuman:\s*\[/', $line)) $current = 'minuman';
    if (preg_match('/tambahan:\s*\[/', $line)) $current = 'tambahan';
    if ($current && preg_match('/\{ name:/', $line)) $counts[$current]++;
}

echo "Makanan items: " . $counts['makanan'] . "\n<br>
";
echo "Minuman items: " . $counts['minuman'] . "\n<br>
";
echo "Tambahan items: " . $counts['tambahan'] . "\n<br>
";
echo "Total items: " . array_sum($counts) . "\n<br>
";

echo "\n=== Items WITH options vs WITHOUT ============\n<br>
";
$withOptions = [];
foreach ($lines as $line) {
    if (preg_match('/name:\s*\'([^\']+)\',\s*options:/', $line, $m)) {
        $withOptions[] = $m[1];
    }
}

// Simple items
$simpleItems = [];
foreach ($lines as $line) {
    if (preg_match('/name:\s*\'([^\']+)\'\s*\},?\s*$/m', $line, $m) && !preg_match('/options:/', $line)) {
        if (!in_array($m[1], $withOptions)) $simpleItems[] = $m[1];
    }
}

echo "Items with 'options': " . count($withOptions) . "\n<br>
";
foreach ($withOptions as $item) echo "  $item\n<br>
";
echo "\nSimple items (no options): " . count($simpleItems) . "\n<br>
";
foreach ($simpleItems as $item) echo "  $item\n<br>
";
