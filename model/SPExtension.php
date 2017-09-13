<?php
/**
 * Copyright (c) 2017. Julian Vöst
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

	function __construct($data) {
		$this->configFile       = $data["configFile"];
		$this->modelPath        = $data["modelPath"];
		$this->templateFiles    = $data["templateFiles"];
		$this->layoutFiles      = $data["layoutFiles"];
		$this->skinFiles        = $data["skinFiles"];
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
	public function getTemplateFiles() {
		return $this->templateFiles;
	}

	/**
	 * @return string[]
	 */
	public function getLayoutFiles() {
		return $this->layoutFiles;
	}

	/**
	 * @return string[]
	 */
	public function getSkinFiles() {
		return $this->skinFiles;
	}

	/**
	 * @return string[]
	 */
	public function getLocaleFiles() {
		return $this->localeFiles;
	}

	public function listAllFiles() {
		echo "Layout Files:" . PHP_EOL;
		foreach($this->getLayoutFiles() as $layoutFile) {
			echo $layoutFile . PHP_EOL;
		}

		echo "Template Files:" . PHP_EOL;
		foreach($this->getTemplateFiles() as $templateFile) {
			echo $templateFile . PHP_EOL;
		}

		echo "Skin Files:" . PHP_EOL;
		foreach($this->getSkinFiles() as $skinFile) {
			echo $skinFile . PHP_EOL;
		}
	}
}