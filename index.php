<?php

$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
$baseUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/'.$uri_segments[1].'/';

header("Location:".$baseUrl.'view' );

?>