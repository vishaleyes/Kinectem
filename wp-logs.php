<div><h1>System Logs</h1><Span><a href="wp-clear.php">Clear Logs</a></Span></div>

<?php

$handle = @fopen("kinectem.txt", "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        echo $buffer;
        echo "<br>";
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
}
fclose($handle);

?>