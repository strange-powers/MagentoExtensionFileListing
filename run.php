<?php 
	require_once '../app/Mage.php';
	Mage::app();

	require_once './model/SPExtension.php';
	require_once './model/SPPackage.php';
	require_once './model/SPTheme.php';

	$moduleName = $argv[1];
	$modules = Mage::getConfig()->getNode('modules')->children();
	$modulesArray = (array)$modules;

	if(!is_null($modulesArray[$moduleName])) {
		$extension = new SPExtension($moduleName);
		$extension->listAllFiles();
	} else {
		echo "Module not found!" . PHP_EOL;
	}

?>