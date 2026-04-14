<?php
$str = '\u098f\u09ae\u09a8 \u09ac\u09be\u0982\u09b2\u09be';
$decoded = json_decode('"' . $str . '"');
echo "Decoded: " . $decoded . "\n";
