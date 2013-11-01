<?php
require_once ('./GetSenateInfoClass.php');
require_once ('ConnectDatabaseClass.php');

$Db = new ConnectDatabaseClass();
$conn = $Db -> startConnect();

$info = new GetSenateInfoClass();
$info -> getSenateInfo();

$Db -> closeConnect($conn);