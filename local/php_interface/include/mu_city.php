<?php
/**
 * Определение и хранение города пользователя (cookie + session + GeoIP).
 *
 * Путь: /local/php_interface/include/mu_city.php
 *
 * MuCity::getCode();   // 'msk' | ''
 * MuCity::getName();   // 'Москва' | ''
 * MuCity::getFilter(); // [] или ['=PROPERTY_CITY' => 'Москва']
 */

class MuCity
{
	const COOKIE_NAME = 'MU_CITY';
	const COOKIE_TTL  = 2592000; // 30 дней
	const SESSION_KEY = 'cityFilter';

	/** @var array|null code => name */
	protected static $cities = null;

	/** @var string|null */
	protected static $currentCode = null;

	public static function getCities()
	{
		if (self::$cities !== null) {
			return self::$cities;
		}

		$path = $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/vendors/cities.json';
		if (is_file($path)) {
			$data = json_decode(file_get_contents($path), true);
			if (is_array($data) && $data) {
				self::$cities = $data;
				return self::$cities;
			}
		}

		self::$cities = [
			'msk' => 'Москва',
			'spb' => 'Санкт-Петербург',
			'nsk' => 'Новосибирск',
			'ekb' => 'Екатеринбург',
			'kzn' => 'Казань',
			'nnv' => 'Нижний Новгород',
			'chl' => 'Челябинск',
			'smr' => 'Самара',
			'oms' => 'Омск',
			'rnd' => 'Ростов-на-Дону',
			'ufa' => 'Уфа',
			'krsk' => 'Красноярск',
			'vrn' => 'Воронеж',
			'prm' => 'Пермь',
			'vlg' => 'Волгоград',
			'krd' => 'Краснодар',
			'srt' => 'Саратов',
			'tyu' => 'Тюмень',
			'tol' => 'Тольятти',
			'izh' => 'Ижевск',
			'brn' => 'Барнаул',
			'uln' => 'Ульяновск',
			'irk' => 'Иркутск',
			'khab' => 'Хабаровск',
			'yar' => 'Ярославль',
			'vlad' => 'Владивосток',
			'mkh' => 'Махачкала',
			'tom' => 'Томск',
			'oren' => 'Оренбург',
			'kem' => 'Кемерово',
			'nvk' => 'Новокузнецк',
			'ryz' => 'Рязань',
			'ast' => 'Астрахань',
			'nbx' => 'Набережные Челны',
			'pen' => 'Пенза',
			'lip' => 'Липецк',
			'kirov' => 'Киров',
			'chel' => 'Чебоксары',
			'kal' => 'Калининград',
			'tul' => 'Тула',
			'kursk' => 'Курск',
			'so' => 'Сочи',
			'stav' => 'Ставрополь',
			'udm' => 'Улан-Удэ',
			'mgt' => 'Магнитогорск',
			'tver' => 'Тверь',
			'ivn' => 'Иваново',
			'bry' => 'Брянск',
			'bel' => 'Белгород',
			'sur' => 'Сургут',
			'vladimir' => 'Владимир',
			'arh' => 'Архангельск',
			'kaluga' => 'Калуга',
			'sml' => 'Смоленск',
			'saransk' => 'Саранск',
			'cherepovets' => 'Череповец',
			'vologda' => 'Вологда',
			'mur' => 'Мурманск',
			'yak' => 'Якутск',
			'grozny' => 'Грозный',
			'tag' => 'Таганрог',
			'kostroma' => 'Кострома',
			'petrozavodsk' => 'Петрозаводск',
			'nizhnevartovsk' => 'Нижневартовск',
			'yoshkarola' => 'Йошкар-Ола',
			'novorossiysk' => 'Новороссийск',
		];

		return self::$cities;
	}

	public static function getCode()
	{
		self::init();
		return (string)self::$currentCode;
	}

	public static function getName()
	{
		$code = self::getCode();
		if ($code === '') {
			return '';
		}
		$cities = self::getCities();
		return $cities[$code] ?? '';
	}

	/**
	 * Фильтр для news.list / CIBlockElement.
	 * В инфоблоках должно быть свойство CITY (строка или список) со значением = название города.
	 */
	public static function getFilter()
	{
		$name = self::getName();
		if ($name === '') {
			return [];
		}
		return ['=PROPERTY_CITY' => $name];
	}

	public static function set($code)
	{
		$code = trim((string)$code);
		$cities = self::getCities();

		if ($code === '' || $code === 'all') {
			self::$currentCode = '';
		} elseif (isset($cities[$code])) {
			self::$currentCode = $code;
		} else {
			return false;
		}

		$_SESSION[self::SESSION_KEY] = self::$currentCode;

		$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		setcookie(
			self::COOKIE_NAME,
			self::$currentCode,
			[
				'expires'  => time() + self::COOKIE_TTL,
				'path'     => '/',
				'domain'   => '',
				'secure'   => $secure,
				'httponly' => false,
				'samesite' => 'Lax',
			]
		);

		return true;
	}

	public static function init()
	{
		if (self::$currentCode !== null) {
			return;
		}

		if (isset($_GET['city'])) {
			self::set($_GET['city']);
			return;
		}

		if (isset($_SESSION[self::SESSION_KEY])) {
			$code = (string)$_SESSION[self::SESSION_KEY];
			$cities = self::getCities();
			if ($code === '' || isset($cities[$code])) {
				self::$currentCode = $code;
				return;
			}
		}

		if (isset($_COOKIE[self::COOKIE_NAME])) {
			$code = (string)$_COOKIE[self::COOKIE_NAME];
			$cities = self::getCities();
			if ($code === '' || isset($cities[$code])) {
				self::$currentCode = $code;
				$_SESSION[self::SESSION_KEY] = $code;
				return;
			}
		}

		$detected = self::detectByIp();
		if ($detected !== null) {
			self::set($detected);
			return;
		}

		self::$currentCode = '';
		$_SESSION[self::SESSION_KEY] = '';
	}

	protected static function detectByIp()
	{
		try {
			if (!class_exists('\Bitrix\Main\Service\GeoIp\Manager')) {
				return null;
			}

			$ip = \Bitrix\Main\Service\GeoIp\Manager::getRealIp();
			if (!$ip || $ip === '127.0.0.1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
				return null;
			}

			$result = \Bitrix\Main\Service\GeoIp\Manager::getDataResult($ip, 'ru');
			if (!$result || !$result->isSuccess()) {
				return null;
			}

			$geo = $result->getGeoData();
			if (!$geo) {
				return null;
			}

			$cityName = trim((string)($geo->cityName ?? ''));
			if ($cityName === '') {
				return null;
			}

			return self::matchCityCode($cityName);
		} catch (\Throwable $e) {
			return null;
		}
	}

	protected static function matchCityCode($cityName)
	{
		$cities = self::getCities();
		$needle = mb_strtolower($cityName);

		foreach ($cities as $code => $name) {
			if (mb_strtolower($name) === $needle) {
				return $code;
			}
		}

		foreach ($cities as $code => $name) {
			$n = mb_strtolower($name);
			if (mb_strpos($needle, $n) !== false || mb_strpos($n, $needle) !== false) {
				return $code;
			}
		}

		return null;
	}
}
