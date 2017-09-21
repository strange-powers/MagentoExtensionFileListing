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

class SPExtension {
	private $configFile;
	private $modelPath;
	private $templateFiles;
	private $layoutFiles;
	private $skinFiles;
	private $localeFiles;
	private $jsFiles;
	private $allFiles;

	function __construct($data) {
		$this->configFile       = $data["configFile"];
		$this->modelPath        = $data["modelPath"];
		$this->templateFiles    = $data["templateFiles"];
		$this->layoutFiles      = $data["layoutFiles"];
		$this->skinFiles        = $data["skinFiles"];
		$this->jsFiles        = $data["jsFiles"];

		$this->allFiles = array(
			$this->configFile,
			$this->modelPath
		);

		$this->allFiles = array_merge($this->allFiles, $this->templateFiles, $this->layoutFiles, $this->skinFiles);
	}

	/**
	 * @return string
	 */
	public function getConfigFile() {
		return $this->configFile;
	}

	/**
	 * @return string[]
	 */
	public function getModelPath() {
		return $this->modelPath;
	}

	/**
	 * @return string[]
	 */
	public function getTemplateFiles($area) {
		return $this->templateFiles[$area];
	}

	/**
	 * @return string[]
	 */
	public function getLayoutFiles($area) {
		return $this->layoutFiles[$area];
	}

	/**
	 * @return string[]
	 */
	public function getSkinFiles($area) {
		return $this->skinFiles[$area];
	}

	/**
	 * @return string[]
	 */
	public function getLocaleFiles() {
		return $this->localeFiles;
	}

	/**
	 * @return string[]
	 */
	public function getJSFiles() {
		return $this->jsFiles;
	}

	/**
	 * @return string[]
	 */
	public function getAllFiles() {
		return $this->allFiles;
	}
}