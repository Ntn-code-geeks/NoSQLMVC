<!doctype html>
<html class="no-js" lang="en">
<?php
$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
$baseUrl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/'.$uri_segments[1].'/';

?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
<!--    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">-->
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/bootstrap.min.css">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/owl.carousel.css">
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/owl.theme.css">
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/normalize.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/main.css">
    <!-- morrisjs CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/morrisjs/morris.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- metisMenu CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/metisMenu/metisMenu-vertical.css">
    <!-- calendar CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/calendar/fullcalendar.min.css">
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/calendar/fullcalendar.print.min.css">
    <!-- forms CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/form/all-type-forms.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="<?=$baseUrl ?>assets/css/responsive.css">
    <!-- modernizr JS
		============================================ -->
    <script src="<?=$baseUrl ?>assets/js/vendor/modernizr-2.8.3.min.js"></script>

        <!-- jquery
        ============================================ -->

    <script src="<?=$baseUrl ?>assets/js/vendor/jquery.min.js"></script>
</head>

<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->

