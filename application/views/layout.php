<html lang="en" ng-app="people">
<head>
    <meta charset="utf-8">
    <title><?=$title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="../dist/css/bootstrap.css" media="screen">
    <script src="../dist/js/angular.min.js"></script>
    <script src="../dist/js/angular-route.min.js"></script>
</head>
    <body>
<?php ($nav)?include("nav.php"):""?>
    <?php include("{$view}.php")?>
<?php include("foot.php")?>