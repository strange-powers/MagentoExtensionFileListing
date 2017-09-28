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

class SPExtensionView {
	/**
	 * Shows found extensions files
	 *
	 * @param SPExtension $extension
	 */
	public function listExtensionFiles($extension) {
		echo "Modules XML:" . PHP_EOL;
		echo $extension->getConfigFile() . PHP_EOL;

		echo "Model Path:" . PHP_EOL;
		echo $extension->getModelPath() . PHP_EOL;

		$this->filesOutput($extension->getJSFiles(), "JS");
		$this->filesOutput($extension->getLocaleFiles(), "Locale");

		foreach(SPTheme::$areas as $area) {
			echo "-------------------------" . PHP_EOL;

			echo "Area: " . $area . PHP_EOL;

			$this->filesOutput($extension->getLayoutFiles($area), "Layout");
			$this->filesOutput($extension->getTemplateFiles($area), "Template");
			$this->filesOutput($extension->getSkinFiles($area), "Skin");
		}
	}

	/**
	 * Output of file section
	 *
	 * @param string $files
	 * @param string $name
	 */
	private function filesOutput($files, $name) {
		echo $name . " Files:" . PHP_EOL;

		if(count($files) > 0) {
			foreach($files as $file) {
				echo $file . PHP_EOL;
			}
		} else {
			echo "No Files detected" . PHP_EOL;
		}
	}
}