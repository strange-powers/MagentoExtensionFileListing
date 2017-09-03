<?php

class SPPackage {
	private $packageName;
	private $packagePath;
	private $themes;

	function __construct($packageName) {
		$this->packageName = $packageName;
		$this->packagePath = Mage::getBaseDir("design") . DS . $packageName;
		$this->themes = array();
		$this->gatherThemes();
	}

	/**
	 * @return SPTheme[]
	 */
	public function getThemes() {
		return $this->themes;
	}

	public function getName() { return $this->packageName; }
	public function getPath() { return $this->packagePath; }

	public function getThemeWithName($name) {
		foreach ($this->themes as $theme) {
			if($theme->getName() == $name) {
				return $theme;
			}
		}

		return null;
	}

	private function gatherThemes() {
		$themeNamesInPackage = Mage::getSingleton('core/design_package')->getThemeList($this->packageName);
		foreach ($themeNamesInPackage as $themeName) {
			$themeToAdd = new SPTheme($themeName, $this->packageName);
			array_push($this->themes, $themeToAdd);
		}
	}

	public static function getAllPackages() {
		$packageNames = Mage::getSingleton('core/design_package')->getPackageList();
		$packages = array();

		foreach($packageNames as $packageName) {
			$package = new SPPackage($packageName);
			array_push($packages, $package);
		}

		return $packages;
	}
}