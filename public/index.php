<?php
require '../vendor/autoload.php';

use Application\Hello;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link href="/css/default.css" rel="stylesheet" />
    <title>Document</title>
</head>
<body>
    <h1>PHP & Mysql</h1>
    <p><?= Hello::message() ?></p>
</body>
</html>