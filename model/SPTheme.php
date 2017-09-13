<?php

/**
 *
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

	public function checkForLayoutFile($file) {
		return $this->checkForFileInPath($file, $this->getLayoutPath());
	}

	public function checkForTemplateFile($file) {
		return $this->checkForFileInPath($file, $this->getTemplatePath());
	}

	public function checkForSkinFile($file) {
		return $this->checkForFileInPath($file, $this->getSkinPath());
	}

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