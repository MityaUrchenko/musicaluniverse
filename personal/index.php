<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личные данные");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:main.profile", 
	"main", 
	array(
		"CHECK_RIGHTS" => "N",
		"SEND_INFO" => "N",
		"SET_TITLE" => "N",
		"USER_PROPERTY" => array(
		),
		"USER_PROPERTY_NAME" => "",
		"COMPONENT_TEMPLATE" => "main"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>