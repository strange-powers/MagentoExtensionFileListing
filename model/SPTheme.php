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


	function __construct($themeName, $package) {
		$this->themeName = $themeName;
		$this->packageName = $package;
		$this->gatherThemeData();
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

	private function gatherThemeData() {
		$designSingleton = Mage::getDesign();
		$infoArr = array(
			"_area"		=> "frontend",
			"_relative" => false,
			"_package"	=> $this->packageName,
			"_theme"	=> $this->themeName
		);

		$this->themePath = $designSingleton->getBaseDir($infoArr);
		$this->skinPath = $designSingleton->getSkinBaseDir($infoArr);

		if(!file_exists($this->skinPath)) {
			$this->skinPath = null;
		}

		$layoutPath = $this->themePath . "layout";
		if(file_exists($layoutPath)) {
			$this->layoutPath = $layoutPath;
		}

		$templatePath = $this->themePath . "template";
		if(file_exists($layoutPath)) {
			$this->templatePath = $templatePath;
		}
	}

	/**
	 * Returns every theme that is installed
	 * @return SPTheme[]
	 */
	public static function getAllThemes() {
		$themeNames = Mage::getSingleton('core/design_package')->getThemeList();
		$themes = array();

		foreach($themeNames as $packageName => $themeNames) {
			foreach($themeNames as $themeName) {
				$theme = new SPTheme($themeName, $packageName);
				array_push($themes, $theme);
			}
		}

		return $themes;
	}
}