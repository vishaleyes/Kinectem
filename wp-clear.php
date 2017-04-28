<div><h1>System Logs</h1><Span><a href="wp-logs.php">Show Logs</a></Span></div>

<?php

$handle = fopen("kinectem.txt", "w");
fwrite($handle, '');
fclose($handle);

?>