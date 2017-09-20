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

class SPThemeController {

	/**
	 * Generates a Theme object
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
			"_relative" => false,
			"_package"	=> $packageName,
			"_theme"	=> $themeName
		);

		foreach(SPTheme::$areas as $area) {
			$infoArr["_area"] = $area;

			$themeData["themePath"][$area] = $designSingleton->getBaseDir($infoArr);
			$themeData["skinPath"][$area] = $designSingleton->getSkinBaseDir($infoArr);

			if(!file_exists($themeData["skinPath"][$area])) {
				$themeData["skinPath"][$area] = null;
			}

			$layoutPath = $themeData["themePath"][$area] . "layout";
			if(file_exists($layoutPath)) {
				$themeData["layoutPath"][$area] = $layoutPath;
			}

			$templatePath = $themeData["themePath"][$area] . "template";
			if(file_exists($layoutPath)) {
				$themeData["templatePath"][$area] = $templatePath;
			}
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
		$controller = new SPThemeController();

		foreach($themeNames as $packageName => $themeNames) {
			foreach($themeNames as $themeName) {
				$theme = $controller->generateTheme($themeName, $packageName);
				array_push($themes, $theme);
			}
		}

		return $themes;
	}
}