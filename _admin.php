<?php
$_menu['Plugins']->addItem('Planning','plugin.php?p=planning','index.php?pf=planning/icon.png',
		preg_match('/plugin.php\?p=planning(&.*)?$/',$_SERVER['REQUEST_URI']),
		$core->auth->isSuperAdmin());

require dirname(__FILE__).'/_widgets.php';
?>