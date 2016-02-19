<?php
$source = file_get_contents($argv[1]);

$re3 = "#defined\(\s*'ABSPATH'\s*\).*\);#";
$rep3 = ' ';
$source = preg_replace($re3, $rep3, $source);

echo $source;