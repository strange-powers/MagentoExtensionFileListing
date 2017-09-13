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
	private $themeName;
	private $themePath;
	private $templatePath;
	private $layoutPath;
	private $skinPath;
	private $packageName;


	function __construct($data) {
		$this->themeName = $data["themeName"];
		$this->themePath = $data["themePath"];
		$this->templatePath = $data["templatePath"];
		$this->layoutPath = $data["layoutPath"];
		$this->skinPath = $data["skinPath"];
		$this->packageName = $data["packageName"];
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
	 * @return String
	 */
	public function getTemplatePath() {
		return $this->templatePath;
	}

	/**
	 * Get the themes layout path
	 * @return String
	 */
	public function getLayoutPath() {
		return $this->layoutPath;
	}

	/**
	 * Get the themes skin path
	 * @return String
	 */
	public function getSkinPath() {
		return $this->skinPath;
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
	 * @param string $file
	 *
	 * @return null|string
	 */
	public function checkForLayoutFile($file) {
		return $this->checkForFileInPath($file, $this->getLayoutPath());
	}

	/**
	 * Checks if file exists in template directory and returns
	 * the complete path if the file exists null if not
	 *
	 * @param string $file
	 *
	 * @return null|string
	 */
	public function checkForTemplateFile($file) {
		return $this->checkForFileInPath($file, $this->getTemplatePath());
	}

	/**
	 * Checks if file exists in skin directory and returns
	 * the complete path if the file exists null if not
	 *
	 * @param string $file
	 *
	 * @return null|string
	 */
	public function checkForSkinFile($file) {
		return $this->checkForFileInPath($file, $this->getSkinPath());
	}

	/**
	 * Checks if file exists in the given directory and returns
	 * the complete path if the file exists null if not
	 *
	 * @param string $file
	 * @param string $path
	 *
	 * @return null|string
	 */
	private function checkForFileInPath($file, $path) {
		if(!is_null($path)) {
			$iterator = new RecursiveDirectoryIterator( $path );
			foreach ( new RecursiveIteratorIterator( $iterator ) as $child ) {
				if ( $child->isDir() ) {
					$filePath = $child->getPath() . DS . $file;
					if ( file_exists( $filePath ) ) {
						return $filePath;
					}
				}
			}
		}

		return null;
	}
}