<?php

class SPExtensionController {
	/**
	 * Gathers data and fills the model object with this data
	 *
	 * @return SPExtension
	 */
	public function generateExtension($extensionName) {
		$modelData = array("name"   => $extensionName);

		$configFile = Mage::getModuleDir('etc', $extensionName) . DS . "config.xml";
		$modelData["configFile"] = $configFile;
		$modelData["modelPath"] = Mage::getModuleDir("", $extensionName);

		$layoutFiles = $this->gatherLayoutFiles($configFile);
		$modelData["layoutFiles"] = $layoutFiles;

		$modelData["templateFiles"] = $this->gatherTemplateFiles($layoutFiles);
		$modelData["skinFiles"] = $this->gatherSkinFiles($layoutFiles);

		return new SPExtension($modelData);
	}

	public function deleteExtension($extension) {

	}

	/**
	 * Returns the found template files
	 *
	 * @param string $layoutFiles
	 *
	 * @return string[]
	 */
	private function gatherTemplateFiles($layoutFiles) {
		$foundPaths = $this->gatherPathsFromLayoutFiles($layoutFiles, array("template"), array("template"));

		return $this->checkPathsInThemes($foundPaths, "checkForTemplateFile");
	}

	/**
	 * Returns the found skin files
	 *
	 * @param string $layoutFiles
	 *
	 * @return string[]
	 */
	private function gatherSkinFiles($layoutFiles) {
		$foundPaths = $this->gatherPathsFromLayoutFiles($layoutFiles, array("stylesheet", "name"), array());

		return $this->checkPathsInThemes($foundPaths, "checkForSkinFile");
	}

	private function gatherLocaleFiles($layoutFiles) {

	}

	/**
	 * Checks if paths do exist in a theme
	 *
	 * @param $paths
	 * @param $checkFunction
	 *
	 * @return string[]
	 */
	private function checkPathsInThemes($paths, $checkFunction) {
		$foundFiles = array();

		foreach (SPTheme::getAllThemes() as $theme) {
			foreach ($paths as $path) {
				if ($foundFile = $theme->$checkFunction($path)) {
					if (!in_array($foundFile, $foundFiles)) {
						array_push($foundFiles, $foundFile);
					}
				}
			}
		}

		return $foundFiles;
	}

	/**
	 * Gathers paths of files found by node in an array of layout files
	 *
	 * @param string $layoutFiles
	 * @param array $nodeNames
	 * @param array $attributeNames
	 *
	 * @return string[]
	 */
	private function gatherPathsFromLayoutFiles($layoutFiles, $nodeNames, $attributeNames) {
		$xmlParser             = new SPXMLParser();
		$foundPaths            = array();

		foreach ($layoutFiles as $layoutFile) {
			$xmlParser->load($layoutFile);

			foreach($attributeNames as $attributeName) {
				foreach($xmlParser->searchForNodesByAttribute($attributeName) as $attributeNode) {
					array_push($foundPaths, $attributeNode->getAttribute($attributeName));
				}
			}

			foreach($nodeNames as $nodeName) {
				foreach($xmlParser->searchForNodesByName($nodeName) as $node) {
					array_push($foundPaths, $node->nodeValue);
				}
			}
		}

		return $foundPaths;
	}

	/**
	 * Gathers paths of layout found in the config file
	 *
	 * @param string $configFile
	 *
	 * @return string[]
	 */
	private function gatherLayoutFiles($configFile) {
		$xmlParser             = new SPXMLParser();
		$xmlParser->load($configFile);
		$gatheredLayoutNodes    = $xmlParser->searchForNodesByName("layout");
		$allThemes              = SPTheme::getAllThemes();
		$availableLayoutFiles   = array();

		foreach($gatheredLayoutNodes as $layoutNode) {
			foreach($layoutNode->getElementsByTagName("file") as $layoutFileNode) {
				$layoutFile = $layoutFileNode->textContent;
				if(!in_array($layoutFile, $availableLayoutFiles)) {
					foreach($allThemes as $theme) {
						if($foundLayoutFile = $theme->checkForLayoutFile($layoutFile)) {
							array_push($availableLayoutFiles, $foundLayoutFile);
						}
					}
				}
			}
		}

		return $availableLayoutFiles;
	}
}