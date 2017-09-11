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
		$extensionController = new SPExtensionController();
		$extension = $extensionController->generateExtension($moduleName);
		$extension->listAllFiles();
	} else {
		echo "Module not found!" . PHP_EOL;
	}

/*
	$layoutFile = "/Applications/XAMPP/xamppfiles/htdocs/magento/app/code/community/Brainvire/Ordercomment/etc/config.xml";
	$xmlParser = new SPXMLParser();
	$xmlParser->load($layoutFile);
	$nodeVal = $xmlParser->searchForNodesByName("layout");
	var_dump($nodeVal[0]);
	$nodeVal = str_replace(" ", "", $nodeVal);
	$nodeVal = str_replace(PHP_EOL, "", $nodeVal);*/