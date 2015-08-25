<?php
/**
 * @package seaccelerator
 */
class Seaccelerator {


	/**
	 * Constructs the Nominations object
	 *
	 * @param modX &$modx A reference to the modX object
	 * @param array $config An array of configuration options
	 */
	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		//$basePath = $this->modx->getOption("seaccelerator.core_path",$config,$this->modx->getOption("core_path")."components/seaccelerator/");
		//$assetsUrl = $this->modx->getOption("seaccelerator.assets_url",$config,$this->modx->getOption("assets_url")."components/seaccelerator/");
		$basePath = "/var/www2/projects/modxdev/__dev/staticelements-accelerator/core/components/seaccelerator/";
		$assetsUrl = MODX_BASE_URL."__dev/staticelements-accelerator/assets/components/seaccelerator/";

		$this->modx->log(xPDO::LOG_LEVEL_ERROR, $assetsUrl);

		$this->config = array_merge(array(
			"basePath" => $basePath,
			"corePath" => $basePath,
			"modelPath" => $basePath."model/",
			"processorsPath" => $basePath."processors/",
			"templatesPath" => $basePath."templates/",
			"chunksPath" => $basePath."elements/chunks/",
			"jsUrl" => $assetsUrl."js/",
			"cssUrl" => $assetsUrl."css/",
			"assetsUrl" => $assetsUrl,
			"connectorUrl" => $assetsUrl."connector.php",
		),$config);

		//$this->modx->addPackage("nomination",$this->config["modelPath"]);
	}

	/**
	 * Initializes the class into the proper context
	 *
	 * @param string $ctx
	 * @return bool|string
	 */
	public function initialize($ctx = "web") {
		switch ($ctx) {
			case "mgr":
				$this->modx->lexicon->load("seaccelerator:default");

				if (!$this->modx->loadClass("seacceleratorControllerRequest",$this->config["modelPath"]."seaccelerator/request/",true,true)) {
					return "Could not load controller request handler.";
				}
				$this->request = new SeacceleratorControllerRequest($this);
				return $this->request->handleRequest();
				break;
		}
		return true;
	}


	public function getListOfFiles () {

	}



}
