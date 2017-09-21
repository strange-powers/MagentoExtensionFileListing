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

		$modelData["jsFiles"] = $this->gatherJSFiles($layoutFiles);

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
	 * @param array $areaLayoutFiles
	 *
	 * @return string[]
	 */
	private function gatherSkinFiles($areaLayoutFiles) {
		$foundPaths = $this->gatherPathsFromLayoutFiles( $areaLayoutFiles, array( "stylesheet", "name" ), array() );

		$xmlParser = new SPXMLParser();

		foreach ( $areaLayoutFiles as $area => $layoutFiles ) {
			foreach ( $layoutFiles as $file ) {
				$xmlParser->load( $file );
				$helperNodes = $xmlParser->searchForNodesByAttribute( "helper" );

				foreach ( $helperNodes as $helperNode ) {
					$helperInfo = $helperNode->getAttribute( "helper" );
					$info = $this->gatherHelperInfo($helperInfo);
					if ( ! is_null( $info ) && $info !== false ) {
						if ( is_array( $info ) ) {
							foreach ( $info as $foundFile ) {
								array_push( $foundPaths[$area], $foundFile );
							}
						} else {
							array_push( $foundPaths[$area], $info );
						}
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
		$foundFiles = array(
			"frontend"  => array(),
			"adminhtml" => array()
		);

		foreach (SPThemeController::getAllThemes() as $theme) {
			foreach ($paths as $area => $areaPaths) {
				foreach($areaPaths as $path) {
					$foundPaths = $theme->$checkFunction($path, $area);
					if (count($foundPaths) > 0) {
						foreach ($foundPaths as $foundFile) {
							if (!in_array($foundFile, $foundFiles[$area])) {
								array_push($foundFiles[$area], $foundFile);
							}
						}
					}
				}
			}
		}

		return $foundFiles;
	}

	private function gatherJSFiles( $areaLayoutFiles ) {
		$xmlParser = new SPXMLParser();
		$gatheredFiles = array();

		$pathCheckFunc = function($file) {
			if(!is_null($file)) {
				$fullPath = Mage::getBaseDir() . DS . "js" . DS . $file;
				if(file_exists($fullPath)) {
					return $fullPath;
				}
			}
		};

		foreach ( $areaLayoutFiles as $area => $layoutFiles ) {
			foreach ( $layoutFiles as $file ) {
				$xmlParser->load($file);
				$jsAttributeNodes = $xmlParser->searchForElementByAttributeContainsValue("method", "addJs");

				foreach($jsAttributeNodes as $attributeNode) {
					$scriptTags = $attributeNode->getElementsByTagName("script");
					foreach($scriptTags as $tag) {
						$file = null;

						if($tagAttribute = $tag->getAttribute("helper")) {
							$file = $this->gatherHelperInfo($tagAttribute);
						} else if($tagValue = $tag->getElementsByTagName("type")) {
							if(strpos($tagValue->item(0)->textContent, "skin") === false) {
								$file = $tag->getElementsByTagName("name")->item(0)->textContent;
							}
						}

						array_push($gatheredFiles, $pathCheckFunc($file));
					}
				}

				$strangeJSFiles = $xmlParser->searchForNodesByName("file"); // In hope for a better variable name
				foreach($strangeJSFiles as $strangeJSFile) {
					array_push($gatheredFiles, $pathCheckFunc($strangeJSFile->textContent));
				}
			}
		}

		return $gatheredFiles;
	}
	
	/**
	 * Gathers paths of files found by node in an array of layout files
	 *
	 * @param array $areaLayoutFiles
	 * @param array $nodeNames
	 * @param array $attributeNames
	 *
	 * @return string[]
	 */
	private function gatherPathsFromLayoutFiles($areaLayoutFiles, $nodeNames, $attributeNames) {
		$xmlParser             = new SPXMLParser();
		$foundPaths      = array(
			"frontend"  => array(),
			"adminhtml" => array()
		);

		foreach($areaLayoutFiles as $areaKey => $layoutFiles) {
			foreach ( $layoutFiles as $layoutFile ) {
				$xmlParser->load( $layoutFile );

				foreach ( $attributeNames as $attributeName ) {
					$attributeNodes = $xmlParser->searchForNodesByAttribute( $attributeName );
					$nodeValues     = SPXMLParser::getAttributeContentFromNodes( $attributeNodes, $attributeName );
					$foundPaths[$areaKey] = array_merge( $nodeValues, $foundPaths[$areaKey] );
				}

				foreach ( $nodeNames as $nodeName ) {
					$nameNodes  = $xmlParser->searchForNodesByName( $nodeName );
					$nodeValues = SPXMLParser::getTextContentFromNodes( $nameNodes );
					$foundPaths[$areaKey] = array_merge($nodeValues, $foundPaths[$areaKey]);
				}
			}
		}

		return $foundPaths;
	}

	private function gatherHelperInfo( $helperInfo ) {
		$helperValeArray = explode( "/", $helperInfo );
		$count           = count( $helperValeArray );
		$functionIndex   = $count - 1;
		$helperFunction  = $helperValeArray[ $functionIndex ];
		unset( $helperValeArray[ $functionIndex ] );
		$helperClassString = implode( "/", $helperValeArray );
		$helperClass       = Mage::helper( $helperClassString );
		$gatheredInfo      = $helperClass->$helperFunction();

		return $gatheredInfo;
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
		$foundLayoutFiles       = array(
			"frontend"  => array(),
			"adminhtml" => array()
		);


		foreach(SPTheme::$areas as $area) {
			$areaNodes = $xmlParser->searchForNodesByName($area);

			foreach($areaNodes as $areaNode) {
				$gatheredLayoutNodes = $areaNode->getElementsByTagName( "layout" );

				foreach ( $gatheredLayoutNodes as $layoutNode ) {
					foreach ( $layoutNode->getElementsByTagName( "file" ) as $layoutFileNode ) {
						array_push( $foundLayoutFiles[ $area ], $layoutFileNode->textContent );
					}
				}
			}
		}

		return $this->checkPathsInThemes($foundLayoutFiles, "checkForLayoutFile");
	}
}