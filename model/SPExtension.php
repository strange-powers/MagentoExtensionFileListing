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

	function __construct($data) {
		$this->configFile       = $data["configFile"];
		$this->modelPath        = $data["modelPath"];
		$this->templateFiles    = $data["templateFiles"];
		$this->layoutFiles      = $data["layoutFiles"];
		$this->skinFiles        = $data["skinFiles"];
		$this->jsFiles          = $data["jsFiles"];
		$this->localeFiles      = $data["translationFiles"];
	}

	/**
	 * Returns the config file that is placed in app/etc/modules
	 *
	 * @return string
	 */
	public function getConfigFile() {
		return $this->configFile;
	}

	/**
	 * Returns the patch to the extension logic
	 *
	 * @return string[]
	 */
	public function getModelPath() {
		return $this->modelPath;
	}

	/**
	 * Returns frontend or adminhtml template files
	 *
	 * @param string $area
	 *
	 * @return string[]
	 */
	public function getTemplateFiles($area) {
		return $this->templateFiles[$area];
	}

	/**
	 * Returns frontend or adminhtml layout files
	 *
	 * @param string $area
	 *
	 * @return string[]
	 */
	public function getLayoutFiles($area) {
		return $this->layoutFiles[$area];
	}

	/**
	 * Returns the frontend or adminhtml skin files
	 *
	 * @param string $area
	 *
	 * @return string[]
	 */
	public function getSkinFiles($area) {
		return $this->skinFiles[$area];
	}

	/**
	 * frontend or adminhtml
	 *
	 * @return string[]
	 */
	public function getLocaleFiles() {
		return $this->localeFiles;
	}

	/**
	 * Returns files which a located in js/
	 *
	 * @return string[]
	 */
	public function getJSFiles() {
		return $this->jsFiles;
	}
}