<?php
	$header = 'Тестирование окружения';

	function check_extension($ext) {
		$is = extension_loaded($ext);
		return function() use($is, $ext) { return array('is' => $is, 'message' => ($is ? 'Загружено' : 'Расширение '.$ext.' не загруженно')); };
	}

	function check_function($function) {
		$disabled = FALSE;
		if ($function[0] == '!') {
			$disabled = TRUE;
			$function = substr($function, 1);
		}
		$is = function_exists($function);
		return function() use ($is, $disabled) {return array('is' => !($is ^ $disabled), 'message' => ($is ? 'Резрешен' : 'Запрещен'));};
	}
	
	function check_ini($option) {
		$disabled = FALSE;
		if ($option[0] == '!') {
			$disabled = TRUE;
			$option = substr($option, 1);
		}
		$is = ini_get($option);
		return function() use ($is, $disabled) {return array('is' => !($is ^ $disabled), 'message' => ($is ? 'Включено' : 'Выключено'));};
	}
	
	$checks = array(
		'Общие настройки' => array(
			'Версия PHP' => function() {
				$is = version_compare(PHP_VERSION, '5.3.0', '>=');
				return array('is' => $is, 'message' => ($is ? PHP_VERSION : 'Разработка требует PHP 5.3.0 или новее, сейчас установленна версия '. PHP_VERSION));
			},
			'Kohana установлена' => function() {
				$is = file_exists('system/classes/kohana/core.php');
				$message = 'Скачать и установить Kohana можно из репозитория <i>https://github.com/kohana/kohana.git</i>';
				if ($is) {
					$core = file_get_contents('system/classes/kohana/core.php');
					if (preg_match('/const VERSION[ ]+= \'(?P<version>[0-9\.]+)\';(.*)const CODENAME = \'(?P<codename>[^\']+)\';/mus', $core, $matches)) {
						$message = $matches['version'].', '.$matches['codename'];
					}
				}
				return array('is' => $is, 'message' => $message);
			},
			'Системная папка' => function() {
				$is = is_file('system/classes/kohana.php');
				return array('is' => $is, 'message' => ($is ? 'Есть' : 'Системная папка Kohana <code>system</code> не существует, или не содержит в себе необходимых файлов'));
			},
			'Папка приложения' => function() {
				$is = is_file('application/bootstrap.php');
				return array('is' => $is, 'message' => ($is ? 'Есть' : 'Папка приложения Kohana <code>application</code> не существует, или не содержит в себе необходимых файлов'));
			},
			'Папка кэширования js-скриптов' => function() {
				$folder = 'public/cache/js';
				$is = is_dir($folder) AND is_writable($folder);
				return array('is' => $is, 'message' => ($is ? 'Доступна' : 'Папка '.$folder.' недоступна для записи'));
			},
			'Папка кэширования стилей' => function() {
				$folder = 'public/cache/css';
				$is = is_dir($folder) AND is_writable($folder);
				return array('is' => $is, 'message' => ($is ? 'Доступна' : 'Папка '.$folder.' недоступна для записи'));
			},
			'Папка файлового кэша' => function() {
				$folder = 'etc/cache';
				$is = is_dir($folder) AND is_writable($folder);
				return array('is' => $is, 'message' => ($is ? 'Доступна' : 'Папка '.$folder.' недоступна для записи'));
			},
			'Папка логов' => function() {
				$folder = 'etc/logs';
				$is = is_dir($folder) AND is_writable($folder);
				return array('is' => $is, 'message' => ($is ? 'Доступна' : 'Папка '.$folder.' недоступна для записи'));
			},
			'PCRE UTF-8' => function() {
				$is = FALSE;
				if ( ! @preg_match('/^.$/u', 'ñ')) {
					$message = '<a href="http://php.net/pcre">PCRE</a> не скомпилены с поддержкой UTF-8';
				} elseif ( ! @preg_match('/^\pL$/u', 'ñ')) {
					$message = '<a href="http://php.net/pcre">PCRE</a> не скомпилены с поддержкой модификатора Unicode property';
				} else {
					$message = 'Да';
					$is = TRUE;
				}
				
				return array('is' => $is, 'message' => $message);
			},
			'Определение URI' => function() {
				$is = isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) OR isset($_SERVER['PATH_INFO']);
				return array('is' => $is, 'message' => ($is ? 'Работает' : 'Недоступна ни одна из переменных <code>$_SERVER[\'REQUEST_URI\']</code>, <code>$_SERVER[\'PHP_SELF\']</code>, <code>$_SERVER[\'PATH_INFO\']'));
			},
		),
		'Настройки PHP' => array(
			'safe_mode'          => check_ini('safe_mode'),
			'magic_quotes_gpc'   => check_ini('magic_quotes_gpc'),
			'short_open_tag'     => check_ini('!short_open_tag'),
			'register_globals'   => check_ini('register_globals'),
			'cgi.fix_pathinfo'   => check_ini('!cgi.fix_pathinfo'),
			'file_uploads'       => check_ini('!file_uploads'),
			'allow_url_fopen'    => check_ini('allow_url_fopen'),
			'allow_url_include'  => check_ini('allow_url_include'),
			'session.auto_start' => check_ini('session.auto_start'),
		),
		'Расширения' => array(
			'Расширение GD'       => check_extension('gd'),
			'Расширение iconv'    => check_extension('iconv'),
			'Расширение http'     => check_extension('http'),
			'Расширение curl'     => check_extension('curl'),
			'Расширение mcrypt'   => check_extension('mcrypt'),
			'Расширение json'     => check_extension('json'),
			'Расширение DOM'      => check_extension('dom'),
			'Расширение XML'      => check_extension('xml'),
			'Расширение Memcache' => function() {
				$ext = 'memcache';
				$is = extension_loaded($ext);
				return array('is' => $is, 'message' => ($is ? 'Загружено' : 'Расширение '.$ext.' не загруженно'));
			},
			'Расширение APC'            => check_extension('apc'),
			'Расширение SimpleXML'      => check_extension('simplexml'),
			'Расширение Suhosin'        => check_extension('suhosin'),
			'Расширение CType'          => check_extension('ctype'),
			'Расширение Reflection API' => check_extension('reflection'),
			'Расширение filter'         => check_extension('filter'),
			'Расширение SPL'            => check_extension('spl'),
			'Расширение PDO'            => check_extension('pdo'),
			'Расширение MySQL'          => check_extension('mysql'),
			'Расширение MySQLi'         => check_extension('mysqli'),
			'Расширение MBstring'       => check_extension('mbstring'),
			'MBstring не перегруженно' => function() {
				$is = ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING;
				return array('is' => !$is, 'message' => ($is ? 'Функции расширения <a href="http://php.net/mbstring">mbstring</a> перегружают нативные функции PHP' : 'Да'));
			},
			'Поддержка FreeType в GD' => function() {
				$is = FALSE;
				$message = 'Нет';
				if (function_exists('gd_info')) {
					$gd = gd_info();
					if ($gd['FreeType Support'] != false) {
						$message = 'Да';
						$is = TRUE;
					}
				}
			
				return array('is' => $is, 'message' => $message);
			}
		),
		'Функции' => array(
			'Функция dl' => function() {
				$is = function_exists('dl') AND ini_get('enable_dl');
				return array('is' => !$is, 'message' => ($is ? 'Разрешена' : 'Запрещена'));
			},
			'Вызов link'            => check_function('link'),				
			'Вызов symlink'         => check_function('symlink'),
			'Вызов system'          => check_function('system'),
			'Вызов shell_exec'      => check_function('shell_exec'),
			'Вызов passthru'        => check_function('passthru'),
			'Вызов exec'            => check_function('exec'),
			'Вызов pcntl_exec'      => check_function('pcntl_exec'),
			'Вызов popen'           => check_function('popen'),
			'Вызов proc_close'      => check_function('proc_close'),
			'Вызов proc_get_status' => check_function('proc_get_status'),
			'Вызов proc_nice'       => check_function('proc_nice'),
			'Вызов proc_open'       => check_function('proc_open'),
			'Вызов proc_terminate'  => check_function('proc_terminate'),
			'Вызов eval'            => check_function('eval'),
			'Вызов php_uname'       => check_function('!php_uname'),
			'Вызов base64_decode'   => check_function('!base64_decode'),
			'Вызов fpassthru'       => check_function('!fpassthru'),
			'Вызов ini_set'         => check_function('!ini_set'),
		),
	);

if (PHP_SAPI !== 'cli'): ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Open Media Group PHP Установка</title>
	<style type="text/css">
	body { width: 42em; margin: 0 auto; font-family: sans-serif; background: #fff; font-size: 1em; }
	h1 { letter-spacing: -0.04em; }
	h1 + p { margin: 0 0 2em; color: #333; font-size: 90%; font-style: italic; }
	code { font-family: monaco, monospace; }
	table { border-collapse: collapse; width: 100%; }
		table th,
		table td { padding: 0.4em; text-align: left; vertical-align: top; }
		table th { width: 40%; font-weight: normal; }
		table tr:nth-child(odd) { background: #eee; }
		table td.pass { color: #191; }
		table td.fail { color: #911; }
	#results { padding: 0.8em; color: #fff; font-size: 1.5em; }
	#results.pass { background: #191; }
	#results.fail { background: #911; }
	#footer p { font-size: 0.7em; }
	</style>
</head>
<body>
	<h1><?php echo $header; ?></h1>
	<p>Этот тест окружения, который проверяет настройки PHP, загруженные модули, а также, установку дополнительного программного обеспечения, которое необходимо для корректной работы приложений, написанных с использованием фреймворка <a href="http://kohanaframework.org/">Kohana</a> или <a href="http://framework.zend.com/">Zend Framework</a>. Также в проверку входит наличие оптимальной файловой структуры и доступность для записи сервером тех, или иных, системных путей.</p>
	
	<?php
		ob_start();
		$total = $failed = 0;
	?>
	<?php foreach ($checks as $title => $_checks): ?>
		<h2><?php echo $title ?></h2>
		<table cellspacing="0">
			<?php foreach ($_checks as $title => $check): ?>
				<tr>
					<th><?php echo $title ?></th>
					<?php
						$result = $check();
						$total++;
						if (!$result['is']) {
							$failed++;
						}
					?>
					<td class="<?php echo ($result['is']) ? 'pass' : 'fail' ?>"><?php echo $result['message'] ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endforeach; ?>
	<?php $data = ob_get_clean(); ?>
	
	<div id="results" class="<?php echo ($failed > 0) ? 'fail' : 'pass' ?>">
		<p>Из <b><?php echo $total; ?></b> тестов <b><?php echo $failed; ?></b> ошибок</p>
	</div>
	
	<?php echo $data; ?>
	
	<div id="footer"><p><b>Host:</b> <?php echo php_uname() ?></ul></div>
</body>
</html>
<?php else:
if (function_exists('exec')) {
	$screen_width = @exec('tput cols');
} else {
	$screen_width = 80;
}

echo "{$header}\n";
echo str_repeat('=', mb_strlen($header, 'UTF-8'))."\n\n";

$total = $failed = 0;

foreach ($checks as $title => $_checks) {
	echo '* '.$title."\n";

	foreach ($_checks as $title => $check) {
		$out = str_repeat(' ', 4).$title;
		
		$result = $check();
		$total++;
		if (!$result['is']) {
			$failed++;
		}
		$out_end = (($result['is']) ? "\033[92m" : "\033[91m").strip_tags($result['message'])."\033[0m\n";
		
		$counts = $screen_width - mb_strlen($out.$out_end, 'UTF-8') + 10; // 10 - цвета
		echo $out.str_repeat(' ', $counts).$out_end;
	}
	
	echo "\n";
}

echo str_repeat('-', $screen_width)."\n";
echo "Всего тестов: {$total}\nОшибок: {$failed}\n";
echo str_repeat('-', $screen_width)."\n";
echo "NB: Конфигурация консольной и серверной версии php могут значительно отличаться!!!\n";

endif;
?>
