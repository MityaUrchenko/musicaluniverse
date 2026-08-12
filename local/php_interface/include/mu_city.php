<?php
/**
 * Определение и хранение города пользователя (cookie + session + GeoIP).
 *
 * Путь: /local/php_interface/include/mu_city.php
 *
 * Отладка: ?city_debug=1
 *
 * Опционально в init.php или перед подключением:
 *   define('MU_SYPEX_KEY', 'ваш_ключ_с_sypexgeo.net');
 */

class MuCity
{
    const COOKIE_NAME = 'MU_CITY';
    const COOKIE_TTL  = 2592000; // 30 дней
    const SESSION_KEY = 'cityFilter';

    /** @var array|null */
    protected static $cities = null;

    /** @var string|null */
    protected static $currentCode = null;

    /** @var array */
    protected static $debugLog = [];

    /** @var bool */
    protected static $debugEnabled = false;

    public static function isDebug()
    {
        return self::$debugEnabled;
    }

    protected static function log($step, $data = null)
    {
        self::$debugLog[] = [
            'step' => $step,
            'data' => $data,
        ];
    }

    public static function renderDebug()
    {
        if (!self::$debugEnabled) {
            return '';
        }

        $lines = [];
        $lines[] = '=== MuCity DEBUG (?city_debug=1) ===';
        $lines[] = 'Итог: code=[' . self::getCode() . '] name=[' . self::getName() . ']';
        $lines[] = 'SESSION[' . self::SESSION_KEY . ']=' . var_export($_SESSION[self::SESSION_KEY] ?? null, true);
        $lines[] = 'COOKIE[' . self::COOKIE_NAME . ']=' . var_export($_COOKIE[self::COOKIE_NAME] ?? null, true);
        $lines[] = 'GET[city]=' . var_export($_GET['city'] ?? null, true);
        $lines[] = '--- шаги ---';
        foreach (self::$debugLog as $i => $row) {
            $lines[] = ($i + 1) . '. ' . $row['step'];
            if ($row['data'] !== null) {
                $lines[] = '   ' . print_r($row['data'], true);
            }
        }

        return '<div id="mu-city-debug" style="position:relative;z-index:99999;background:#fff3cd;color:#333;border:2px solid #ffc107;padding:12px 16px;margin:0;font:13px/1.4 monospace;white-space:pre-wrap;word-break:break-all;">'
            . htmlspecialchars(implode("\n", $lines), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            . '</div>';
    }

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
                self::log('cities loaded from cities.json', ['count' => count($data), 'path' => $path]);
                return self::$cities;
            }
            self::log('cities.json invalid', $path);
        } else {
            self::log('cities.json not found, built-in list', $path);
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

    public static function getFilter()
    {
        $name = self::getName();
        if ($name === '') {
            return [];
        }
        return ['=PROPERTY_CITY' => $name];
    }

    public static function set($code, $byUser = false)
    {
        $code = trim((string)$code);
        $cities = self::getCities();

        if ($code === '' || $code === 'all') {
            self::$currentCode = '';
        } elseif (isset($cities[$code])) {
            self::$currentCode = $code;
        } else {
            self::log('set() rejected unknown code', $code);
            return false;
        }

        $_SESSION[self::SESSION_KEY] = self::$currentCode;

        if ($byUser || self::$currentCode !== '') {
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
            self::log('cookie written', ['value' => self::$currentCode, 'byUser' => $byUser]);
        }

        self::log('set() done', [
            'code' => self::$currentCode,
            'name' => self::$cities[self::$currentCode] ?? '',
            'byUser' => $byUser,
        ]);

        return true;
    }

    public static function init()
    {
        if (self::$currentCode !== null) {
            return;
        }

        self::$debugEnabled = !empty($_GET['city_debug']);
        self::log('init() start', [
            'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? '',
            'HTTP_X_FORWARDED_FOR' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
        ]);

        $cities = self::getCities();

        if (isset($_GET['city'])) {
            self::log('source: GET[city]', $_GET['city']);
            self::set($_GET['city'], true);
            return;
        }

        if (array_key_exists(self::COOKIE_NAME, $_COOKIE)) {
            $code = (string)$_COOKIE[self::COOKIE_NAME];
            self::log('source: COOKIE', $code);
            if ($code === '' || isset($cities[$code])) {
                self::$currentCode = $code;
                $_SESSION[self::SESSION_KEY] = $code;
                return;
            }
            self::log('cookie value not in cities list, ignore');
        }

        if (!empty($_SESSION[self::SESSION_KEY])) {
            $code = (string)$_SESSION[self::SESSION_KEY];
            self::log('source: SESSION', $code);
            if (isset($cities[$code])) {
                self::$currentCode = $code;
                return;
            }
            self::log('session value not in cities list, ignore');
        }

        self::log('source: GeoIP auto-detect');
        $detected = self::detectByIp();
        if ($detected !== null && $detected !== '') {
            self::log('GeoIP matched city code', $detected);
            self::set($detected, false);
            return;
        }

        self::log('GeoIP did not match any city → Все города');
        self::$currentCode = '';
        $_SESSION[self::SESSION_KEY] = '';
    }

    protected static function detectByIp()
    {
        $ip = self::getClientIp();
        self::log('client IP', $ip);

        if (!$ip) {
            self::log('IP empty');
            return null;
        }

        if (self::isPrivateIp($ip)) {
            self::log('IP is private/local — GeoIP skipped');
            return null;
        }

        // Цепочка провайдеров: Bitrix → Sypex → ip-api.com → ipapi.co
        $providers = [
            'Bitrix' => function ($ip) {
                return self::detectViaBitrix($ip);
            },
            'Sypex' => function ($ip) {
                return self::detectViaSypexRest($ip);
            },
            'ip-api.com' => function ($ip) {
                return self::detectViaIpApi($ip);
            },
            'ipapi.co' => function ($ip) {
                return self::detectViaIpApiCo($ip);
            },
        ];

        $cityName = '';
        foreach ($providers as $name => $fn) {
            try {
                $cityName = (string)$fn($ip);
            } catch (\Throwable $e) {
                self::log($name . ' exception', $e->getMessage());
                $cityName = '';
            }
            self::log($name . ' cityName', $cityName !== '' ? $cityName : '(empty)');
            if ($cityName !== '') {
                break;
            }
        }

        if ($cityName === '') {
            self::log('no city name from any GeoIP source');
            return null;
        }

        $code = self::matchCityCode($cityName);
        self::log('matchCityCode', ['input' => $cityName, 'result' => $code]);

        return $code;
    }

    protected static function getClientIp()
    {
        try {
            if (class_exists('\Bitrix\Main\Service\GeoIp\Manager')) {
                $ip = \Bitrix\Main\Service\GeoIp\Manager::getRealIp();
                self::log('Bitrix getRealIp()', $ip);
                if ($ip) {
                    return $ip;
                }
            } else {
                self::log('Bitrix GeoIp\\Manager class not found');
            }
        } catch (\Throwable $e) {
            self::log('getRealIp exception', $e->getMessage());
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($parts[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    protected static function isPrivateIp($ip)
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }
        if (strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return true;
        }
        if (preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip)) {
            return true;
        }
        return false;
    }

    protected static function httpGet($url, $timeout = 3)
    {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => $timeout,
                'ignore_errors' => true,
                'header' => "User-Agent: MuCity/1.1\r\nAccept: application/json\r\n",
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $raw = @file_get_contents($url, false, $ctx);
        return ($raw === false) ? '' : $raw;
    }

    protected static function detectViaBitrix($ip)
    {
        if (!class_exists('\Bitrix\Main\Service\GeoIp\Manager')) {
            self::log('detectViaBitrix: Manager missing');
            return '';
        }

        $result = \Bitrix\Main\Service\GeoIp\Manager::getDataResult($ip, 'ru');
        if (!$result) {
            self::log('detectViaBitrix: null result');
            return '';
        }
        if (!$result->isSuccess()) {
            $errors = method_exists($result, 'getErrorMessages') ? $result->getErrorMessages() : [];
            self::log('detectViaBitrix: not success', $errors);
            return '';
        }

        $geo = $result->getGeoData();
        if (!$geo) {
            self::log('detectViaBitrix: empty geoData');
            return '';
        }

        self::log('detectViaBitrix: geoData raw', [
            'cityName' => $geo->cityName ?? null,
            'regionName' => $geo->regionName ?? null,
            'countryName' => $geo->countryName ?? null,
            'countryCode' => $geo->countryCode ?? null,
        ]);

        return trim((string)($geo->cityName ?? ''));
    }

    /**
     * Sypex Geo REST.
     * Без ключа — лимит ~10k/мес на IP/домен.
     * Ключ: define('MU_SYPEX_KEY', '...');
     */
    protected static function detectViaSypexRest($ip)
    {
        $key = defined('MU_SYPEX_KEY') ? (string)MU_SYPEX_KEY : '';
        if ($key !== '') {
            $url = 'https://api.sypexgeo.net/' . rawurlencode($key) . '/json/' . urlencode($ip);
        } else {
            $url = 'https://api.sypexgeo.net/json/' . urlencode($ip);
        }
        self::log('Sypex REST request', $url);

        $raw = self::httpGet($url);
        if ($raw === '') {
            self::log('Sypex REST: empty response');
            return '';
        }

        self::log('Sypex REST raw (first 400)', mb_substr($raw, 0, 400));
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return '';
        }

        if (!empty($data['error'])) {
            self::log('Sypex REST error', $data['error']);
            return '';
        }

        if (!empty($data['city']['name_ru'])) {
            return trim((string)$data['city']['name_ru']);
        }
        if (!empty($data['city']['name_en'])) {
            return trim((string)$data['city']['name_en']);
        }

        return '';
    }

    /**
     * ip-api.com — бесплатно, без ключа (до ~45 запросов/мин с одного IP сервера)
     * Только HTTP на free-тарифе.
     */
    protected static function detectViaIpApi($ip)
    {
        $url = 'http://ip-api.com/json/' . urlencode($ip) . '?lang=ru&fields=status,message,city,regionName,country,countryCode';
        self::log('ip-api.com request', $url);

        $raw = self::httpGet($url);
        if ($raw === '') {
            self::log('ip-api.com: empty response');
            return '';
        }

        self::log('ip-api.com raw', mb_substr($raw, 0, 400));
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return '';
        }

        if (($data['status'] ?? '') !== 'success') {
            self::log('ip-api.com fail', $data['message'] ?? $data);
            return '';
        }

        return trim((string)($data['city'] ?? ''));
    }

    /**
     * ipapi.co — запасной HTTPS без ключа (лимит по IP)
     */
    protected static function detectViaIpApiCo($ip)
    {
        $url = 'https://ipapi.co/' . urlencode($ip) . '/json/';
        self::log('ipapi.co request', $url);

        $raw = self::httpGet($url);
        if ($raw === '') {
            self::log('ipapi.co: empty response');
            return '';
        }

        self::log('ipapi.co raw', mb_substr($raw, 0, 400));
        $data = json_decode($raw, true);
        if (!is_array($data) || !empty($data['error'])) {
            self::log('ipapi.co fail', $data['reason'] ?? $data);
            return '';
        }

        return trim((string)($data['city'] ?? ''));
    }

    protected static function matchCityCode($cityName)
    {
        $cities = self::getCities();
        $needle = mb_strtolower(trim($cityName));
        $needle = preg_replace('/^(г\.|город)\s*/ui', '', $needle);

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
