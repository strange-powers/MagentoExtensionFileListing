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

class SPTheme {
	public static $areas = array("adminhtml", "frontend");

	private $themeName;
	private $themePath;
	private $templatePath;
	private $layoutPath;
	private $skinPath;
	private $packageName;


	function __construct($data) {
		$this->themeName = $data["themeName"];
		$this->packageName = $data["packageName"];
		$this->themePath = $data["themePath"];
		$this->templatePath = $data["templatePath"];
		$this->layoutPath = $data["layoutPath"];
		$this->skinPath = $data["skinPath"];
	}

	/**
	 * Get the themes name
	 * @return String
	 */
	public function getThemeName() {
		return $this->themeName;
	}

	/**
	 * Get the themes basepath
	 * @return String
	 */
	public function getThemePath() {
		return $this->themePath;
	}

	/**
	 * Get the themes template path
	 *
	 * @param $area string
	 *
	 * @return String
	 */
	public function getTemplatePath($area) {
		return $this->templatePath[$area];
	}

	/**
	 * Get the themes layout path
	 *
	 * @param $area string
	 *
	 * @return String
	 */
	public function getLayoutPath($area) {
		return $this->layoutPath[$area];
	}

	/**
	 * Get the themes skin path
	 *
	 * @param $area string
	 *
	 * @return String
	 */
	public function getSkinPath($area) {
		return $this->skinPath[$area];
	}

	/**
	 * Get the themes packages name
	 * @return String
	 */
	public function getPackageName() {
		return $this->packageName;
	}

	/**
	 * Checks if file exists in layout directory and returns
	 * the complete path if the file exists null if not
	 *
	 * @param string $area
	 * @param string $file
	 *
	 * @return string[]
	 */
	public function checkForLayoutFile($file, $area) {
		$layoutFiles = array();

		if($areaPath = $this->getLayoutPath($area)) {
			$checkedFile = SPDirectoryHelper::getSingleInstance()->checkForFileInPath($file, $areaPath);
			if(!is_null($checkedFile) && strlen($checkedFile) > 0) {
				array_push($layoutFiles, $checkedFile);
			}
		}

		return $layoutFiles;
	}

	/**
	 * Checks if file exists in template directory and returns
	 * the complete path if the file exists null if not
	 *
	 * @param string $area
	 * @param string $file
	 *
	 * @return string[]
	 */
	public function checkForTemplateFile($file, $area) {
		$layoutFiles = array();

		if($areaPath = $this->getTemplatePath($area)) {
			$checkedFile = SPDirectoryHelper::getSingleInstance()->checkForFileInPath($file, $areaPath, true);
			if(!is_null($checkedFile) && strlen($checkedFile) > 0) {
				array_push($layoutFiles, $checkedFile);
			}
		}


		return $layoutFiles;
	}

	/**
	 * Checks if file exists in skin directory and returns
	 * the complete path if the file exists null if not
	 *
	 * @param string $area
	 * @param string $file
	 *
	 * @return string[]
	 */
	public function checkForSkinFile($file, $area) {
		$layoutFiles = array();

		if($areaPath = $this->getSkinPath($area)) {
			$checkedFile = SPDirectoryHelper::getSingleInstance()->checkForFileInPath($file, $areaPath);
			if(!is_null($checkedFile) && strlen($checkedFile) > 0) {
				array_push($layoutFiles, $checkedFile);
			}
		}

		return $layoutFiles;
	}
}