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
	 * @class Mage
	 *
	 * @param string $extensionName
	 *
	 * @return SPExtension
	 */
	public function generateExtension($extensionName) {
		$modelData = array(
			"name"          => $extensionName
		);

		$modelData["configFile"] = Mage::getBaseDir("etc") . DS . "modules" . DS . $extensionName . ".xml";
		$modelData["modelPath"] = Mage::getModuleDir("", $extensionName);

		$configFile = Mage::getModuleDir('etc', $extensionName) . DS . "config.xml";
		$configXmlParser = new SPXMLParser();
		$configXmlParser->load($configFile);

		$layoutFiles = $this->gatherLayoutFiles($configXmlParser);
		$modelData["layoutFiles"] = $layoutFiles;

		/* Be careful the lambda function is placed in a loop */
		$lambdaReturnArr = array(
			"templateFiles" => array(),
			"skinFiles"     => array(),
			"jsFiles"       => array()
		);
		$gatheredFiles = $this->loadLayoutFiles($lambdaReturnArr, $layoutFiles, function($xmlParser, $area, &$result) use ($modelData) {
			$result["templateFiles"]   = array_merge($result["templateFiles"], $this->gatherTemplateFiles($xmlParser, $area));
			$result["skinFiles"]       = array_merge($result["skinFiles"], $this->gatherSkinFiles($xmlParser, $area));
			$result["jsFiles"]         = array_merge($result["jsFiles"], $this->gatherJSFiles($xmlParser));
		});

		$modelData["templateFiles"] = $this->checkPathsInThemes($gatheredFiles["templateFiles"], "checkForTemplateFile");
		$modelData["skinFiles"] = $this->checkPathsInThemes($gatheredFiles["skinFiles"], "checkForSkinFile");
		$modelData["jsFiles"] = $gatheredFiles["jsFiles"];

		return new SPExtension($modelData);
	}

	/**
	 * Returns the found template files
	 *
	 * @param SPXMLParser $xmlParser
	 * @param string $area
	 *
	 * @return string[]
	 */
	private function gatherTemplateFiles($xmlParser, $area) {
		return $this->gatherPathsFromLayoutFiles($xmlParser, $area, array("template"), array("template"));
	}

	/**
	 * Returns the found skin files
	 *
	 * @param SPXMLParser $xmlParser
	 * @param string $area
	 *
	 * @return string[]
	 */
	private function gatherSkinFiles($xmlParser, $area) {
		$foundPaths = $this->gatherPathsFromLayoutFiles( $xmlParser, $area, array( "stylesheet", "name" ), array() );

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

		return $foundPaths;
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

	/**
	 * Gathers all JS Files
	 *
	 * @param SPXMLParser $xmlParser
	 *
	 * @return string[]
	 */
	private function gatherJSFiles( $xmlParser ) {
		$gatheredFiles = array();
		$jsAttributeNodes = $xmlParser->searchForElementByAttributeContainsValue("method", "addJs");
		$jsAttributeNodes = array_merge($jsAttributeNodes, $xmlParser->searchForNodesByName("file"));

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

				if(!is_null($file)) {
					$fullPath = Mage::getBaseDir() . DS . "js" . DS . $file;
					if(file_exists($fullPath)) {
						array_push($gatheredFiles, $fullPath);
					}
				}
			}
		}

		return $gatheredFiles;
	}
	
	/**
	 * Gathers paths of files found by node in an array of layout files
	 *
	 * @param SPXMLParser $xmlParser
	 * @param string $areaKey
	 * @param array $nodeNames
	 * @param array $attributeNames
	 *
	 * @return string[]
	 */
	private function gatherPathsFromLayoutFiles($xmlParser, $areaKey, $nodeNames, $attributeNames) {
		$foundPaths      = array(
			$areaKey  => array()
		);

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

		return $foundPaths;
	}

	/**
	 * Iterates through layout files and expects a lambda function
	 * which a xml parser, the area key, and a output variable is provided as parameters
	 *
	 * @param string[] $areaLayoutFiles
	 * @param Closure $lambda
	 * @param mixed $outputType
	 *
	 * @return array
	 */
	private function loadLayoutFiles($outputType, $areaLayoutFiles, $lambda) {
		$xmlParser = new SPXMLParser();

		foreach($areaLayoutFiles as $areaKey => $layoutFiles) {
			foreach ( $layoutFiles as $layoutFile ) {
				$xmlParser->load($layoutFile);
				$lambda($xmlParser, $areaKey, $outputType);
			}
		}

		return $outputType;
	}

	/**
	 * Gathers data which is in a magento helper object
	 *
	 * @param string $helperInfo
	 *
	 * @return string
	 */
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
	 * @param SPXMLParser $configXmlParser
	 *
	 * @return string[]
	 */
	private function gatherLayoutFiles($configXmlParser) {
		$foundLayoutFiles       = array(
			"frontend"  => array(),
			"adminhtml" => array()
		);


		foreach(SPTheme::$areas as $area) {
			$areaNodes = $configXmlParser->searchForNodesByName($area);

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