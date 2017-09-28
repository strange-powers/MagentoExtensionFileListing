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


class SPDirectoryHelper {
	/**
	 * Provides the singleton
	 *
	 * @return SPDirectoryHelper
	 */
	public static function getSingleInstance() {
		static $instance = null;

		if(is_null($instance)) {
			$instance = new SPDirectoryHelper();
		}

		return $instance;
	}

	/**
	 * Checks if file exists in the given directory and returns
	 * the complete path (included subdirectories) if the file exists null if not
	 *
	 * @param string $file
	 * @param string $path
	 *
	 * @return null|string|string[]
	 */
	public function checkForFileInPath($file, $path) {
		$iterator = new RecursiveDirectoryIterator($path);
		$foundFiles = array();

		foreach(new RecursiveIteratorIterator($iterator) as $child) {
			if($child->isDir()) {
				$filePath = $child->getPath(). DS . $file;
				if(file_exists($filePath)) {
					return $filePath;
				}
			}
		}

		return null;
	}
}