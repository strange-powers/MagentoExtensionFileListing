<?php

class SPThemeController {

	/**
	 * Gnerates a Theme object
	 *
	 * @param $themeName
	 * @param $packageName
	 *
	 * @return SPTheme
	 */
	public function generateTheme($themeName, $packageName) {
		$designSingleton = Mage::getDesign();
		$themeData = array(
			"themeName"     => $themeName,
			"packageName"   => $packageName
		);

		$infoArr = array(
			"_area"		=> "frontend",
			"_relative" => false,
			"_package"	=> $packageName,
			"_theme"	=> $themeName
		);

		$themeData["themePath"] = $designSingleton->getBaseDir($infoArr);
		$themeData["skinPath"] = $designSingleton->getSkinBaseDir($infoArr);

		if(!file_exists($themeData["skinPath"])) {
			$themeData["skinPath"] = null;
		}

		$layoutPath = $themeData["themePath"] . "layout";
		if(file_exists($layoutPath)) {
			$themeData["layoutPath"] = $layoutPath;
		}

		$templatePath = $themeData["themePath"] . "template";
		if(file_exists($layoutPath)) {
			$themeData["templatePath"] = $templatePath;
		}

		return new SPTheme($themeData);
	}

	/**
	 * Returns every theme that is installed
	 * @return SPTheme[]
	 */
	public static function getAllThemes() {
		$themeNames = Mage::getSingleton('core/design_package')->getThemeList();
		$themes = array();

		foreach($themeNames as $packageName => $themeNames) {
			foreach($themeNames as $themeName) {
				$theme = new SPTheme($themeName, $packageName);
				array_push($themes, $theme);
			}
		}

		return $themes;
	}
}