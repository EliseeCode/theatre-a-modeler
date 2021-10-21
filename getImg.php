<?php 
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename='.basename($newfile));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize("../voUpload/img/box.png"));
header("Content-Type: " . $result['mime_type']);
readfile("../vocUpload/img/box.png");
?>
