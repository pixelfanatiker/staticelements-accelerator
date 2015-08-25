<?php
/**
 * @package seaccelerator
 */
class Seaccelerator {

	private $elementsDirectory;


	/**
	 * Constructs the Seaccelerator object
	 *
	 * @param modX &$modx A reference to the modX object
	 * @param array $config An array of configuration options
	 */
	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		//$basePath = $this->modx->getOption("seaccelerator.core_path", null, $this->modx->getOption("core_path")."components/seaccelerator/");
		//$assetsUrl = $this->modx->getOption("seaccelerator.assets_url", null, $this->modx->getOption("assets_url")."components/seaccelerator/");
		$basePath = "/var/www2/projects/modxdev/staticelements-accelerator/core/components/seaccelerator/";
		$assetsUrl = MODX_BASE_URL."staticelements-accelerator/assets/components/seaccelerator/";

		//$this->modx->log(xPDO::LOG_LEVEL_ERROR, $assetsUrl);

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


	/**
	 * @return mixed
	 */
	public function getMediaSource () {
		$mediaSourceId = $this->modx->getOption ('seaccelerator.mediasource', null, 2);
		return $mediaSourceId;
	}


	/**
	 * @param $source
	 * @return bool
	 */
	public  function getMediaSourceName ($mediaSourceId) {
		$mediaSource = $this->modx->getObject('sources.modMediaSource', $mediaSourceId);
		if(!empty($mediaSource) && is_object($mediaSource)) {
			return $mediaSource->get("name");
		} else {
			return false;
		}
	}


	/**
	 * @param $elementsPath
	 * @param $mediaSourceId
	 * @return mixed
	 */
	public function getMediaSourcePath ($elementsPath, $mediaSourceId) {
		$mediaSource = $this->modx->getObject('sources.modMediaSource', $mediaSourceId);
		if(!empty($mediaSource) && is_object($mediaSource)) {
			$elementsPath = $mediaSource->prepareOutputUrl($elementsPath);
		}

		return $elementsPath;
	}


	/**
	 * @return string
	 */
	public function getElementsFilesystemPath () {
		$mediaSourceId = $this->getMediaSource();
		$elementsPath = $this->modx->getOption("seaccelerator.elements_directory", null, 'elements/');
		if ($mediaSourceId > 0) {
			$elementsPath = MODX_BASE_PATH.$this->getMediaSourcePath($elementsPath, $mediaSourceId);
		} else {
			$elementsPath = MODX_ASSETS_PATH.$elementsPath;
		}
		//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "[getElementsPath] elementsPath: ".$elementsPath);

		return $elementsPath;
	}


	/**
	 * @return array
	 */
	public function getNewFiles () {

		// TODO: refactor
		$actionCreateElement = json_decode('{"className":"check-square-o js_actionLink js_createElement","text":"Create element"}');
		$actionEditFile = json_decode('{"className":"edit js_actionLink js_editFile","text":"Edit file"}');
		$actionDeleteFile = json_decode('{"className":"trash js_actionLink js_deleteFile","text":"Delete file"}');

		$actions = array($actionCreateElement, $actionEditFile, $actionDeleteFile);

		$newFiles = array();
		$filesystem = $this->scanElementsDirectory();

		foreach ($filesystem as $file) {
			if ($this->isElementAlreadyImported($file)) {
				$filePathArray = $this->getFilePath($file);
				$fileNameString = array_shift ($filePathArray);
				$fileType = $this->getFileType($filePathArray);
				$mediaSourceId = $this->getMediaSource();
				$mediaSourceName = $this->getMediaSourceName($mediaSourceId);

				$useCategories = $this->modx->getOption ('seaccelerator.use_categories', null, true);

				$this->modx->log(xPDO::LOG_LEVEL_ERROR, "file: ".$file);

				if ($useCategories) {
					$category = $this->getElementCategoryFromFilesystem($filePathArray);
				} else {
					$category = 0;
				}

				//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "Filename: ".$filename."; path: ".$filePath);

				if ($mediaSourceId > 0) {
					$filePathString = $this->getFilePathAsString($filePathArray);
				} else {
					$elementsPath = $this->getElementsFilesystemPath();
					$filePathString = $elementsPath.$this->getFilePathAsString($filePathArray);
				}

				$newFiles[] = array(
					'filename' => $fileNameString,
					'category' => $category,
					'type' => $fileType,
					'path' => $filePathString,
					'content' => file_get_contents($file, true),
					'mediasource' => $mediaSourceId,
					'mediasourceName' => $mediaSourceName,
					'actions' => $actions
				);
			}
		}

		return $newFiles;
	}


	/**
	 * @param $filePathArray
	 * @return string
	 */
	public function getFilePathAsString ($filePathArray) {

		$filePathString = implode('/', array_reverse($filePathArray));

		return $filePathString;
	}


	/**
	 * @param $fileNameString
	 * @return array
	 */
	public function getFilePath ($fileNameString) {

		$elementsPath = $this->getElementsFilesystemPath();
		$filePath = array_reverse (explode ('/', str_replace ($elementsPath, '', $fileNameString)));
		$this->modx->log(xPDO::LOG_LEVEL_ERROR, "elementsPath: ".$elementsPath."; path: ".$filePath);
		return $filePath;
	}


	/**
	 * @param $filePath
	 * @return int|string
	 */
	public function getElementCategoryFromFilesystem ($filePath) {

		$fullCategory = array_reverse ($filePath);
		array_shift ($fullCategory);
		array_pop ($fullCategory);
		$fullCategory = implode ('/', $fullCategory);
		$fullCategory = $fullCategory . '/';

		$category = $fullCategory;
		if ($category == '/') {
			$category = 0;
		}

		return $category;
	}


	/**
	 * @return array
	 */
	public function scanElementsDirectory () {
		$files = array ();
		$path = $this->getElementsFilesystemPath();
		$this->_scanFolder ($path, $files);
		return $files;
	}


	/**
	 * @param $path
	 * @param $files
	 */
	private function _scanFolder ($path, &$files) {
		$directory = new RecursiveDirectoryIterator($path);
		foreach (new RecursiveIteratorIterator($directory) as $filename => $file) {
			$rest = substr($filename, -2);
			if ($rest != '/.' && $rest != '..') {
				$files[] = $filename;
			}
		}
	}


	/**
	 * @param $file
	 * @return bool
	 */
	public function isElementAlreadyImported ($file) {

		$filePath = $this->getFilePath($file);

		$fileName = array_reverse (explode ('.', array_pop (explode ('/', $file))));
		$filePath = array_reverse (explode ('/', str_replace ($filePath, '', $file)));

		//$fileExtension = implode ('.', array_reverse (array_slice ($fileName, 0, $position)));
		$fileType = $this->getFileType($filePath);

		$modElementClasses = array (
			"chunks" => "modChunk"
		,"plugins" => "modPlugin"
		,"snippets" => "modSnippet"
		,"templates" => "modTemplate"
		);

		foreach ($modElementClasses as $type => $modClass) {
			if ($fileType == $type) {
				if (!is_object ($this->getStaticElement($file, $modClass))) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * @param $filePath
	 * @return int|mixed
	 */
	public function getFileType ($filePath) {

		$type = array_pop ($filePath);
		if ($type == "") {
			$type = 0;
		}

		return $type;
	}


	/**
	 * @param $file
	 * @param $modClass
	 * @return null|object
	 */
	public function getStaticElement ($file, $modClass) {
		$parameter = array (
			'static' => 1
		,'static_file:LIKE' => "%".$file."%",
		);
		$element = $this->modx->getObject ($modClass, $parameter);

		return $element;
	}


}
