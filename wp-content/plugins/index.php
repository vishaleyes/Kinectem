<?php
// Silence is golden.
$fp = fopen('kinectem.txt', 'a+');
        fwrite($fp, "\r\r\n<div style='background-color:#F2F2F2; color:#222279; font-weight: bold; padding:10px;box-shadow: 0 5px 2px rgba(0, 0, 0, 0.25);'>");
        fwrite($fp, "<b>API call Time</b> : <font size='6' style='color:orange;'><b><i>" . $dt . "</i></b></font> <br>");
        fwrite($fp, "<b>Function Name</b> : <font size='6' style='color:orange;'><b><i>" . Yii::app()->controller->action->id . "</i></b></font>");
        fwrite($fp, "\r\r\n\n");
        fwrite($fp, "<b>PARAMS</b> : " . print_r($_REQUEST, true));
        fwrite($fp, "\r\r\n");
        $link = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . '' . print_r($_SERVER['REQUEST_URI'], true) . "";
        fwrite($fp, "<b>URL</b> :<a style='text-decoration:none;color:#4285F4' target='_blank' href='" . $link . "'> " . $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . '' . print_r($_SERVER['REQUEST_URI'], true) . "</a>");
        fwrite($fp, "</div>\r\r\n");
        fclose($fp);
