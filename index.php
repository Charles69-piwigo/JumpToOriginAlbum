<?php
// Recursive directory listing protection.
if (!isset($_SERVER['REQUEST_URI']))
{
  $_SERVER['REQUEST_URI'] = '';
}
$url = 'http://'.$_SERVER['HTTP_HOST'].preg_replace('#/[^/]*$#', '/', $_SERVER['REQUEST_URI']);
header('Request-URI: '.$url);
header('Content-Location: '.$url);
header('Location: '.$url);
exit();
?>
