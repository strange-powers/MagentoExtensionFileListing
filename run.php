<?php 
	require_once '../app/Mage.php';
	Mage::app();

	require_once './model/SPExtension.php';
	require_once './model/SPPackage.php';
	require_once './model/SPTheme.php';
	require_once "./controller/SPXMLParser.php";
	require_once "./controller/SPExtensionController.php";

	$moduleName = $argv[1];
	$modules = Mage::getConfig()->getNode('modules')->children();
	$modulesArray = (array)$modules;

	if(!is_null($modulesArray[$moduleName])) {
		$extensionController = new SPExtensionController($moduleName);
		$extension = $extensionController->fillExtensionWithData();
		$extension->listAllFiles();
	} else {
		echo "Module not found!" . PHP_EOL;
	}