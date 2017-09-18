<?php
/**
 * Copyright (c) 2017. Julian Vöst <jv@strange-powers.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * See <http://www.gnu.org/licenses/>.
 */

class SPExtensionController {
	/**
	 * Gathers data and fills the model object with this data
	 *
	 * @param string $extensionName
	 *
	 * @return SPExtension
	 */
	public function generateExtension($extensionName) {
		$modelData = array("name"   => $extensionName);

		$modelData["configFile"] = Mage::getBaseDir("etc") . DS . "modules" . DS . $extensionName . ".xml";
		$modelData["modelPath"] = Mage::getModuleDir("", $extensionName);

		$configFile = Mage::getModuleDir('etc', $extensionName) . DS . "config.xml";
		$layoutFiles = $this->gatherLayoutFiles($configFile);
		$modelData["layoutFiles"] = $layoutFiles;

		$modelData["templateFiles"] = $this->gatherTemplateFiles($layoutFiles);
		$modelData["skinFiles"] = $this->gatherSkinFiles($layoutFiles);

		return new SPExtension($modelData);
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

		$xmlParser = new SPXMLParser();

		foreach($layoutFiles as $file) {
			$xmlParser->load($file);
			$helperNode = $xmlParser->searchForNodesByAttribute("helper");
			foreach($helperNode as $node) {
				$helperVal       = $node->getAttribute("helper");
				$helperValeArray = explode("/", $helperVal);
				$count           = count($helperValeArray);
				$functionIndex  = $count - 1;
				$helperFunction  = $helperValeArray[$functionIndex];
				unset($helperValeArray[$functionIndex]);
				$helperClassString = implode( "/", $helperValeArray);
				$helperClass       = Mage::helper( $helperClassString );
				$skinFile          = $helperClass->$helperFunction();

				if(!is_null($skinFile) && $skinFile !== false) {
					if (is_array($skinFile)) {
						foreach ($skinFile as $foundFile) {
							array_push($foundPaths, $foundFile);
						}
					} else {
						array_push($foundPaths, $skinFile);
					}
				}
			}
		}

		return $this->checkPathsInThemes($foundPaths, "checkForSkinFile");
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

		foreach (SPThemeController::getAllThemes() as $theme) {
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
				$attributeNodes = $xmlParser->searchForNodesByAttribute($attributeName);
				$nodeValues = SPXMLParser::getAttributeContentFromNodes($attributeNodes, $attributeName);
				$foundPaths = array_merge($nodeValues, $foundPaths);
			}

			foreach($nodeNames as $nodeName) {
				$nameNodes = $xmlParser->searchForNodesByName($nodeName);
				$nodeValues = SPXMLParser::getTextContentFromNodes($nameNodes);
				$foundPaths = array_merge($nodeValues, $foundPaths);
			}
		}

		return $foundPaths;
	}

	/**
	 * Gathers paths of layout files found in the config file
	 *
	 * @param string $configFile
	 *
	 * @return string[]
	 */
	private function gatherLayoutFiles($configFile) {
		$xmlParser             = new SPXMLParser();
		$xmlParser->load($configFile);
		$gatheredLayoutNodes    = $xmlParser->searchForNodesByName("layout");
		$foundLayoutFiles       = array();

		foreach($gatheredLayoutNodes as $layoutNode) {
			foreach($layoutNode->getElementsByTagName("file") as $layoutFileNode) {
				array_push($foundLayoutFiles, $layoutFileNode->textContent);
			}
		}

		return $this->checkPathsInThemes($foundLayoutFiles, "checkForLayoutFile");
	}
}