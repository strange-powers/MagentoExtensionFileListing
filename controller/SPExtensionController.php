<?php

class SPExtensionController {
	private $extensionModel;
	private $xmlParser;
	private $moduleName;

	/**
	 * Every Nodename has a function where the nodename has to be searched for
	 * @var array
	 */
	private static $templateNodeFunctions = array(
		"template"      => "checkForTemplateFile",
		"stylesheet"    => "checkForSkinFile",
		"name"          => "checkForSkinFile"
	);

	public function __construct($moduleName) {
		$this->extensionModel = new SPExtension($moduleName);
		$this->xmlParser = new SPXMLParser();
	}

	/**
	 * Gathers data and fills the model object with this data
	 * @return SPExtension
	 */
	public function fillExtensionWithData() {
		$modelData = array("name"   => $this->moduleName);

		$configFile = Mage::getModuleDir('etc', $this->moduleName) . DS . "config.xml";
		$modelData["configFile"] = $configFile;
		$modelData["modelPath"] = Mage::getModuleDir("", $this->moduleName);
		$layoutFiles = $this->gatherLayoutFiles($configFile);
		$modelData["layoutFiles"] = $layoutFiles;

		$modelData["templateFiles"] = $this->gatherTemplateFiles($layoutFiles);
		$modelData["skinFiles"] = $this->gatherSkinFiles($layoutFiles);

		$this->extensionModel = new SPExtension($modelData);

		return $this->extensionModel;
	}

	public function deleteExtension() {

	}

	/**
	 * Returns the found template files
	 *
	 * @param string $layoutFiles
	 *
	 * @return string[]
	 */
	private function gatherTemplateFiles($layoutFiles) {
		return $this->gatherDataFromLayoutFiles($layoutFiles,"template");
	}

	/**
	 * Returns the found skin files
	 *
	 * @param string $layoutFiles
	 *
	 * @return string[]
	 */
	private function gatherSkinFiles($layoutFiles) {
		return array_merge($this->gatherDataFromLayoutFiles($layoutFiles,"stylesheet"), $this->gatherDataFromLayoutFiles($layoutFiles,"name"));
	}

	/**
	 * Gathers paths of files found by node in an array of layout files
	 *
	 * @param string $layoutFiles
	 * @param string $nodeName
	 *
	 * @return string[]
	 */
	private function gatherDataFromLayoutFiles($layoutFiles, $nodeName) {
		$allThemes             = SPTheme::getAllThemes();
		$foundFiles            = array();
		$themeFunction         = (string) $this->templateNodeFunctions[ $nodeName ];

		foreach ($layoutFiles as $layoutFile) {
			$this->xmlParser->setXmlFile($layoutFile);
			$fileNodes = $this->xmlParser->searchForNodeName( $nodeName );

			foreach ($fileNodes as $node) {
				foreach ($allThemes as $theme) {
					if ($foundFile = $theme->$themeFunction($node->nodeValue)) {
						if (!in_array( $foundFile, $foundFiles)) {
							array_push($foundFiles, $foundFile);
						}
					}
				}
			}
		}

		return $foundFiles;
	}

	/**
	 * Gathers paths of layout found in the config fil
	 *
	 * @param string $configFile
	 *
	 * @return string[]
	 */
	private function gatherLayoutFiles($configFile) {
		$this->xmlParser->setXmlFile($configFile);
		$gatheredLayoutNodes    = $this->xmlParser->searchForNodeName("layout");
		$availableLayoutFiles   = array();

		foreach($gatheredLayoutNodes as $layoutNode) {
			$layoutFile = str_replace(" ", "", $layoutNode->nodeValue);
			$layoutFile = str_replace(PHP_EOL, "", $layoutFile);

			if(!in_array($layoutFile, $availableLayoutFiles)) {
				foreach(SPTheme::getAllThemes() as $theme) {
					if($foundLayoutFile = $theme->checkForLayoutFile($layoutFile)) {
						array_push($availableLayoutFiles, $foundLayoutFile);
					}
				}
			}
		}

		return $availableLayoutFiles;
	}
}