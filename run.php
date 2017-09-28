<?php 
	require_once '../app/Mage.php';
	Mage::app();

	require_once "./utils/SPDirectoryHelper.php";

	require_once './model/SPExtension.php';
	require_once './model/SPTheme.php';

	require_once './controller/SPThemeController.php';
	require_once "./controller/SPXMLParser.php";
	require_once "./controller/SPExtensionController.php";

	require_once "./view/SPExtensionView.php";

	$moduleName = $argv[1];
	$modules = Mage::getConfig()->getNode('modules')->children();
	$modulesArray = (array)$modules;

	if(!is_null($modulesArray[$moduleName])) {
		$extensionController = new SPExtensionController();
		$extension = $extensionController->generateExtension($moduleName);

		$view = new SPExtensionView();
		$view->listExtensionFiles($extension);
	} else {
		echo "Module not found!" . PHP_EOL;
	}