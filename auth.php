<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
global $USER;
$USER->authorize(1);
unlink($_SERVER["DOCUMENT_ROOT"] . "/auth.php");
header('Location: /bitrix/');
?>