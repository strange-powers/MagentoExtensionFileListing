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

		echo "JS Path:" . PHP_EOL;
		foreach($extension->getJSFiles() as $jsFile) {
			echo $jsFile . PHP_EOL;
		}

		echo PHP_EOL;

		foreach(SPTheme::$areas as $area) {
			echo "-------------------------" . PHP_EOL;

			echo "Area: " . $area . PHP_EOL;

			echo "Layout Files:" . PHP_EOL;
			foreach($extension->getLayoutFiles($area) as $layoutFile) {
				echo $layoutFile . PHP_EOL;
			}

			echo "Template Files:" . PHP_EOL;
			foreach($extension->getTemplateFiles($area) as $templateFile) {
				echo $templateFile . PHP_EOL;
			}

			echo "Skin Files:" . PHP_EOL;
			foreach($extension->getSkinFiles($area) as $skinFile) {
				echo $skinFile . PHP_EOL;
			}
		}
	}
}