<?php
/**
 *
 */
class SPExtension {
	private $configFile;
	private $modelPath;
	private $templateFiles;
	private $layoutFiles;
	private $skinFiles;

	function __construct(String $moduleName) {
		$this->modelPath = Mage::getModuleDir('', $moduleName);
		$this->configFile = Mage::getModuleDir('etc', $moduleName) . DS . "config.xml";
		$this->templateFiles = array();
		$this->layoutFiles = array();
		$this->skinFiles = array();

		$this->gatherLayoutFiles();
		$this->gatherTemplateFiles();
		$this->gatherSkinFiles();
	}

	/**
	 * @return string
	 */
	public function getConfigFile() {
		return $this->configFile;
	}

	/**
	 * @return string
	 */
	public function getModelPath() {
		return $this->modelPath;
	}

	/**
	 * @return array
	 */
	public function getTemplateFiles() {
		return $this->templateFiles;
	}

	/**
	 * @return array
	 */
	public function getLayoutFiles() {
		return $this->layoutFiles;
	}

	/**
	 * @return array
	 */
	public function getSkinFiles() {
		return $this->skinFiles;
	}



	public function delete () {

	}

	public function listAllFiles() {
		echo "Layout Files:" . PHP_EOL;
		foreach($this->getLayoutFiles() as $layoutFile) {
			echo $layoutFile . PHP_EOL;
		}

		echo "Template Files:" . PHP_EOL;
		foreach($this->getTemplateFiles() as $templateFile) {
			echo $templateFile . PHP_EOL;
		}

		echo "Skin Files:" . PHP_EOL;
		foreach($this->getSkinFiles() as $skinFile) {
			echo $skinFile . PHP_EOL;
		}
	}

	private function gatherLayoutFiles() {
		$xmlDom = new DOMDocument();
		$xmlDom->load($this->configFile);
		$availableLayoutFiles = array();

		foreach($xmlDom->getElementsByTagName("layout") as $layoutNode) {
			$layoutFile = str_replace(" ", "", $layoutNode->nodeValue);
			$layoutFile = str_replace(PHP_EOL, "", $layoutFile);
			if(!in_array($layoutFile, $availableLayoutFiles)) {
				array_push($availableLayoutFiles, $layoutFile);
			}
		}


		foreach(SPTheme::getAllThemes() as $theme) {
			foreach($availableLayoutFiles as $layoutFile) {
				if($foundLayoutFile = $theme->checkForLayoutFile($layoutFile)) {
					array_push($this->layoutFiles, $foundLayoutFile);
				}
			}
		}
	}

	private function gatherTemplateFiles() {
		$this->templateFiles = $this->gatherDataFromLayoutFiles("template");
	}

	private function gatherSkinFiles() {
		$this->skinFiles = array_merge($this->gatherDataFromLayoutFiles("stylesheet"), $this->gatherDataFromLayoutFiles("name"));
	}

	private function gatherDataFromLayoutFiles($nodeNameData) {
		$xmlDom = new DOMDocument();
		$allThemes = SPTheme::getAllThemes();
		$foundFiles = array();
		$templateNodeFunctions = array(
			"template"      => "checkForTemplateFile",
			"stylesheet"    => "checkForSkinFile",
			"name"          => "checkForSkinFile"
		);
		$themeFunction = (string) $templateNodeFunctions[$nodeNameData];

		foreach($this->getLayoutFiles() as $layoutFile) {
			$xmlDom->load($layoutFile);
			foreach($xmlDom->getElementsByTagName($nodeNameData) as $node) {
				foreach($allThemes as $theme) {
					if($foundFile = $theme->$themeFunction($node->nodeValue)) {
						if(!in_array($foundFile, $foundFiles)) {
							array_push($foundFiles, $foundFile);
						}
					}
				}
			}
		}

		return $foundFiles;
	}
}