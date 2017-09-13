<?php 
	require_once '../app/Mage.php';
	Mage::app();

	require_once './model/SPExtension.php';
	require_once './model/SPPackage.php';
	require_once './model/SPTheme.php';

	require_once './controller/SPThemeController.php';
	require_once "./controller/SPXMLParser.php";
	require_once "./controller/SPExtensionController.php";

	$moduleName = $argv[1];
	$modules = Mage::getConfig()->getNode('modules')->children();
	$modulesArray = (array)$modules;

	if(!is_null($modulesArray[$moduleName])) {
		$extensionController = new SPExtensionController();
		$extension = $extensionController->generateExtension($moduleName);
		$extension->listAllFiles();
	} else {
		echo "Module not found!" . PHP_EOL;
	}

/*
	$layoutFile = "/Applications/XAMPP/xamppfiles/htdocs/magento/app/design/frontend/base/default/layout/iways_paypalplus.xml";
	$dom = new SPXMLParser();
	$dom->load($layoutFile);
	$com = $dom->getCommentedXML();
	var_dump($com);*/