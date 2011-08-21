<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
	$write_enabled = array(
		'Кэширование скриптов' => 'public/cache/js',
		'Кэширование стилей' => 'public/cache/css',
		'Файловый кэш' => 'etc/cache',
		'Папка логов' => 'etc/logs',
	);
	$modules = array(
		'gd' => TRUE,
		'iconv' => TRUE,
		'http' => FALSE,
		'curl' => TRUE,
		'mcrypt' => FALSE,
		'json' => TRUE,
		'dom' => TRUE,
		'xml' => TRUE,
		'memcache' => TRUE,
		'memcached' => FALSE,
		'apc' => TRUE,
		'simplexml' => TRUE,
		'suhosin' => FALSE,
		'ctype' => TRUE,
		'reflection' => TRUE,
		'filter' => TRUE,
		'spl' => TRUE,
		'pdo' => FALSE,
		'mysql' => TRUE,
		'mysqli' => TRUE,
	);
	$disabled_functions = array('link', 'symlink', 'system', 'shell_exec', 'passthru', 'exec', 'pcntl_exec', 'popen', 'proc_close', 'proc_get_status', 'proc_nice', 'proc_open', 'proc_terminate', 'eval');
	$enabled_functions = array('php_uname', 'base64_decode', 'fpassthru', 'ini_set');
	
	$ini_get = array('safe_mode', 'magic_quotes_gpc', '!short_open_tag','register_globals','!cgi.fix_pathinfo','!file_uploads','allow_url_fopen','allow_url_include','session.auto_start');
?>
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
		table td.warn { color: #991; }
		table td.info { color: #119; }
	#results { padding: 0.8em; color: #fff; font-size: 1.5em; }
	#results.pass { background: #191; }
	#results.info { background: #119; }
	#results.fail { background: #911; }
	#results.warn { background: #991; }
	</style>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript">
		$(function() {
			var total = $('tr').length;
			var warns = $('tr td.warn').length;
			var fails = $('tr td.fail').length;
			$('#results b.all').text(total);
			$('#results b.warn').text(warns);
			$('#results b.fail').text(fails);
			
			var type = 'pass';
			if (warns > 0) type = 'warn';
			if (fails > 0) type = 'fail';
			$('#results').addClass(type);
		});
	</script>
</head>
<body>

	<h1>Тестирование окружения</h1>

	<p>
		Этот тест окружения, который проверяет настройки PHP, загруженные модули, а также, установку дополнительного программного
		обеспечения, которое необходимо для корректной работы приложений, написанных с использованием фреймворка
		<a href="http://kohanaframework.org/">Kohana</a> или <a href="http://framework.zend.com/">Zend Framework</a>.
		Также в проверку входит наличие оптимальной файловой структуры и доступность для записи сервером тех, или иных, системных путей.
	</p>
	<div id="results">
		<p>Из <b class="all">0</b> тестов предупреждений - <b class="warn">0</b> и ошибок - <b class="fail">0</b></p>
	</div>

	<table cellspacing="0">
		<tr>
			<th>Версия PHP</th>
			<?php if (version_compare(PHP_VERSION, '5.3.0', '>=')): ?>
				<td class="pass"><?php echo PHP_VERSION ?></td>
			<?php else: ?>
				<td class="fail">Разработка требует PHP 5.3.0 или новее, сейчас установленна версия <?php echo PHP_VERSION ?></td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Установленна Kohana</th>
			<?php if (file_exists('system/classes/kohana/core.php')): $kohana_enabled = TRUE; ?>
				<?php
				    $version = 'Да';
				    $core = file_get_contents('system/classes/kohana/core.php');
				    if (preg_match('/const VERSION[ ]+= \'(?P<version>[0-9\.]+)\';(.*)const CODENAME = \'(?P<codename>[^\']+)\';/mus', $core, $matches)) {
					$version = $matches['version'].', '.$matches['codename'];
				    }
				?>
				<td class="pass"><?php echo $version ?></td>
			<?php else: $kohana_enabled = FALSE; ?>
				<td class="info">Скачать и установить Kohana можно из репозитория <i>https://github.com/kohana/kohana.git</i></td>
			<?php endif ?>
		</tr>
		<?php if ($kohana_enabled): ?>
		<tr>
			<th>Системная папка</th>
			<?php if (is_file('system/classes/kohana.php')): ?>
				<td class="pass"><?php echo './system/' ?></td>
			<?php else: ?>
				<td class="fail">Системная папка Kohana <code>system</code> не существует, или не содержит в себе необходимых файлов</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Папка приложения</th>
			<?php if (is_file('application/bootstrap.php')): ?>
				<td class="pass"><?php echo './application/' ?></td>
			<?php else: ?>
				<td class="fail">Папка приложения Kohana <code>application</code> не существует, или не содержит в себе необходимых файлов</td>
			<?php endif ?>
		</tr>
		<?php endif ?>
		
		<?php foreach ($write_enabled as $title => $folder): ?>
		<tr>
			<th><?php echo $title ?></th>
			<?php if (is_dir($folder) AND is_writable($folder)): ?>
				<td class="pass"><?php echo $folder ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Папка <code><?php echo $folder ?></code> недоступна для записи</td>
			<?php endif ?>
		</tr>
		<?php endforeach; ?>
		
		<tr>
			<th>PCRE UTF-8</th>
			<?php if ( ! @preg_match('/^.$/u', 'ñ')): ?>
				<td class="fail"><a href="http://php.net/pcre">PCRE</a> не скомпилены с поддержкой UTF-8</td>
			<?php elseif ( ! @preg_match('/^\pL$/u', 'ñ')): ?>
				<td class="fail"><a href="http://php.net/pcre">PCRE</a> не скомпилены с поддержкой модификатора Unicode property</td>
			<?php else: ?>
				<td class="pass">Да</td>
			<?php endif ?>
		</tr>
		
		<tr>
			<th>Определение URI</th>
			<?php if (isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) OR isset($_SERVER['PATH_INFO'])): ?>
				<td class="pass">Да</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Недоступна ни одна из переменных <code>$_SERVER['REQUEST_URI']</code>, <code>$_SERVER['PHP_SELF']</code>, <code>$_SERVER['PATH_INFO']</code></td>
			<?php endif ?>
		</tr>
	</table>
	
	<h2>Расширения</h2>
	<table cellspacing="0">
		<?php if (extension_loaded('mbstring')): ?>
		<tr>
			<th>MBstring не перегруженно</th>
			<?php if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING): ?>
				<td class="fail">Функции расширения <a href="http://php.net/mbstring">mbstring</a> перегружают нативные функции PHP</td>
			<?php else: ?>
				<td class="pass">Да</td>
			<?php endif ?>
		</tr>
		<?php endif ?>

		<?php foreach ($modules as $name => $require): ?>
		<tr>
			<th>Расширение <?php echo $name ?></th>
			<?php if (extension_loaded($name)): ?>
				<td class="pass">Включено</td>
			<?php else: $failed = TRUE ?>
				<td class="<?php echo ($require) ? 'fail' : 'warn'; ?>">Расширение <a href="http://php.net/<?php echo $name ?>"><?php echo $name ?></a> не загружено или не скомпилировано</td>
			<?php endif ?>
		</tr>
		<?php endforeach ?>
		<tr>
			<th>Freetype в GD доступен</th>
			<?php if (function_exists('gd_info')): ?>
				<?php $gd = gd_info(); ?>
				<?php if ($gd['FreeType Support'] == false): ?>
					<td class="fail">Нужен <a href="http://php.net/gd">GD</a> с вкомпиленной поддержкой FreeType</td>
				<?php else: ?>
					<td class="pass">Да</td>
				<?php endif ?>
			<?php else: ?>
				<td class="fail">Необходим модуль <a href="http://php.net/gd">GD</a></td>
			<?php endif ?>
		</tr>
	</table>
	
	<h2>Функции</h2>
	<table cellspacing="0">	
		<?php foreach ($disabled_functions as $function): ?>
			<?php if (function_exists($function)): ?>
			<tr>
				<th>Функция <?php echo $function ?></th>
				<td class="warn">Разрешена</td>
			</tr>
			<?php endif ?>
		<?php endforeach ?>
		
		<?php foreach ($enabled_functions as $function): ?>
			<?php if ( ! function_exists($function)): ?>
			<tr>
				<th>Функция <?php echo $function ?></th>
				<td class="fail">Запрещена</td>
			</tr>
			<?php endif ?>
		<?php endforeach ?>
		
		<tr>
			<th>Функция dl</th>
			<?php if (function_exists('dl') AND ini_get('enable_dl')): ?>
				<td class="fail">Разрешена</td>
			<?php else: ?>
				<td class="pass">Выключена</td>
			<?php endif ?>
		</tr>
	</table>
	
	<h2>Настройки PHP</h2>
	<table cellspacing="0">
		<?php foreach ($ini_get as $config): ?>
		<tr>
			<?php
				$allowed = TRUE;
				if ($config[0] == '!') {
					$allowed = FALSE;
					$config = substr($config, 1);
				}
			?>
			<th>Параметр <?php echo $config ?></th>
			<?php if (ini_get($config)): ?>
				<td class="<?php echo ($allowed) ? 'fail' : 'pass' ?>">Разрешен</td>
			<?php else: ?>
				<td class="<?php echo ($allowed) ? 'pass' : 'fail' ?>">Выключен</td>
			<?php endif ?>
		</tr>
		<?php endforeach ?>
	</table>

</body>
</html>
