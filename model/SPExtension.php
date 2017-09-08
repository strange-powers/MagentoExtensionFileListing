<?php
/**
 *
 */
class SPExtension {
	private $configFile;
	private $modelPath;
	private $templateFiles;
	private $layoutFiles;
	private $skinFiles;

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
	 * @return string
	 */
	public function getModelPath() {
		return $this->modelPath;
	}

	/**
	 * @return array
	 */
	public function getTemplateFiles() {
		return $this->templateFiles;
	}

	/**
	 * @return array
	 */
	public function getLayoutFiles() {
		return $this->layoutFiles;
	}

	/**
	 * @return array
	 */
	public function getSkinFiles() {
		return $this->skinFiles;
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