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
	 * @param SPExtensionController $extensionController
	 */
	public function listExtensionFiles($extension, $extensionController) {
		echo "Modules XML:" . PHP_EOL;
		echo $extension->getConfigFile() . PHP_EOL;

		echo "Model Path:" . PHP_EOL;
		echo $extension->getModelPath() . PHP_EOL;

		echo "Layout Files:" . PHP_EOL;
		foreach($extension->getLayoutFiles() as $layoutFile) {
			echo $layoutFile . PHP_EOL;
		}

		echo "Template Files:" . PHP_EOL;
		foreach($extension->getTemplateFiles() as $templateFile) {
			echo $templateFile . PHP_EOL;
		}

		echo "Skin Files:" . PHP_EOL;
		foreach($extension->getSkinFiles() as $skinFile) {
			echo $skinFile . PHP_EOL;
		}

		$this->askForExtensionDestruction($extension, $extensionController);
	}

	/**
	 * Asks the user if he/she wants to delete the found extension files
	 *
	 * @param SPExtension $extension
	 * @param SPExtensionController $extController
	 */
	public function askForExtensionDestruction($extension, $extController) {
		echo "Do want to delete all these files?  Type 'yes' to continue: ";
		$handle = fopen ("php://stdin","r");
		$line = fgets($handle);
		fclose($handle);

		if(trim($line) != 'yes') {
			echo "Okay LOL!" . PHP_EOL;
			exit;
		}

		$extController->deleteExtension($extension);
	}
}