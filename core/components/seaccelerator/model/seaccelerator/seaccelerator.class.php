<?php
/**
 * @package seaccelerator
 */
class Seaccelerator {

	public $modx = null;
	public $map = array();
	public $config = array();
	public $modElementClasses = array(
		"templates" => array("modTemplate"),
		"chunks" => array("modChunk"),
		"snippets" => array("modSnippet"),
		"plugins" => array("modPlugin")
	);
	//public $elementsDirectory;


	/**
	 * Constructs the Seaccelerator object
	 *
	 * @param modX &$modx A reference to the modX object
	 * @param array $config An array of configuration options
	 */
	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$basePath = $this->modx->getOption("seaccelerator.core_path", null, $this->modx->getOption("core_path")."components/seaccelerator/");
		$assetsUrl = $this->modx->getOption("seaccelerator.assets_url", null, $this->modx->getOption("assets_url")."components/seaccelerator/");

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

		$this->modx->addPackage("seaccelerator",$this->config["modelPath"]);
	}

	/**
	 * Initializes the class into the proper context
	 *
	 * @param string $ctx
	 * @return bool|string
	 */
	public function initialize($ctx = "web") {
		switch($ctx) {
			case "mgr":
				$this->modx->lexicon->load("seaccelerator:default");

				if(!$this->modx->loadClass("seacceleratorControllerRequest",$this->config["modelPath"]."seaccelerator/request/",true,true)) {
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
	public function getMediaSource() {

		$mediaSourceId = $this->modx->getOption("seaccelerator.mediasource", null, 1);

		return $mediaSourceId;
	}


	/**
	 * @param $mediaSourceId
	 * @return bool
	 */
	public  function getMediaSourceName($mediaSourceId) {

		$mediaSource = $this->modx->getObject("sources.modMediaSource", $mediaSourceId);
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
	public function getMediaSourcePath($elementsPath, $mediaSourceId) {

		$mediaSource = $this->modx->getObject("sources.modMediaSource", $mediaSourceId);
		if(!empty($mediaSource) && is_object($mediaSource)) {
			$elementsPath = $mediaSource->prepareOutputUrl($elementsPath);
		}

		return $elementsPath;
	}


	/**
	 * @return string
	 */
	public function getElementsFilesystemPath() {
		$mediaSourceId = $this->getMediaSource();
		$elementsDirectory = $this->modx->getOption("seaccelerator.elements_directory", null, "elements/");
		if($mediaSourceId == 1) {
			$elementsPath = MODX_BASE_PATH . $this->getMediaSourcePath($elementsDirectory, $mediaSourceId) . "elements/";
		} else if ($mediaSourceId > 1) {
			$elementsPath = MODX_BASE_PATH . $this->getMediaSourcePath($elementsDirectory, $mediaSourceId);
		} else {
			$elementsPath = MODX_ASSETS_PATH.$elementsDirectory;
		}

		return $elementsPath;
	}


	/**
	 * @return array
	 */
	public function getNewFiles() {

		// TODO: refactor
		$actionCreateElement = json_decode('{"className":"check-square-o js_actionLink js_createElement","text":"Create element"}');
		$actionEditFile = json_decode('{"className":"edit js_actionLink js_editFile","text":"Edit file"}');
		$actionDeleteFile = json_decode('{"className":"trash js_actionLink js_deleteFile","text":"Delete file"}');

		$actions = array($actionCreateElement, $actionEditFile, $actionDeleteFile);

		$newFiles = array();
		$elementsPath = $this->getElementsFilesystemPath();
		$filesystem = $this->scanElementsDirectory($elementsPath);

		foreach($filesystem as $file) {
			$filePathArray = $this->getFilePathAsArray($file);
			$fileName = array_shift ($filePathArray);
			$fileType = $this->getFileType($filePathArray);

			if($this->isElementNotStatic($fileName, $fileType)) {
				$mediaSourceId = $this->getMediaSource();
				$mediaSourceName = $this->getMediaSourceName($mediaSourceId);
				$category = $this->getElementCategoryFromFilesystem($filePathArray);

				if($mediaSourceId > 0) {
					$filePathString = $this->convertFilePathToString($filePathArray);
				} else {
					$filePathString = $elementsPath.$this->convertFilePathToString($filePathArray);
				}

				$newFiles[] = array(
					"filename" => $fileName,
					"category" => $category,
					"type" => $fileType,
					"path" => $filePathString,
					"content" => file_get_contents($file, true),
					"mediasource" => $mediaSourceId,
					"mediasourceName" => $mediaSourceName,
					"actions" => $actions
				);
			}
		}

		return $newFiles;
	}


	/**
	 * @param $filePathArray
	 * @return string
	 */
	public function convertFilePathToString($filePathArray) {

		$filePathString = implode("/", array_reverse($filePathArray));

		return $filePathString;
	}


	/**
	 * @param $fileNameString
	 * @return array
	 */
	public function getFilePathAsArray($fileNameString) {

		$elementsPath = $this->getElementsFilesystemPath();
		$filePath = array_reverse(explode("/", str_replace($elementsPath, "", $fileNameString)));

		return $filePath;
	}


	/**
	 * @param $fileName
	 * @param $filePath
	 * @param $mediaSourceId
	 * @param $makeFullPath
	 * @return string
	 */
	public function makeStaticElementFilePath($fileName, $filePath, $mediaSourceId, $makeFullPath) {

		$elementsPath = $this->modx->getOption("seaccelerator.elements_directory", null, "elements/");
		if($makeFullPath == true) {
			$elementsPath = $this->getMediaSourcePath($filePath, $mediaSourceId);
			$staticElementFilePath = MODX_BASE_PATH.$elementsPath."/".$fileName;

		} else if($mediaSourceId > 0) {
			$staticElementFilePath = $filePath."/".$fileName;

		} else {
			$staticElementFilePath = MODX_ASSETS_PATH.$elementsPath.$filePath."/".$fileName;
		}

		return $staticElementFilePath;
	}


	/**
	 * @param $fileName
	 * @param $filePath
	 * @return string
	 */
	public function makeFilesystemPath($fileName, $filePath) {

		$file = MODX_BASE_PATH.$filePath."/".$fileName;

		return $file;
	}


	/**
	 * @param $filePath
	 * @return int|mixed|string
	 */
	public function getElementCategoryFromFilesystem($filePath) {

		$useCategories = $this->modx->getOption("seaccelerator.use_categories", null, true);
		if($useCategories) {
			$fullCategory = array_reverse($filePath);
			array_shift($fullCategory);
			array_pop($fullCategory);
			$fullCategory = implode("/", $fullCategory);
			$fullCategory = $fullCategory . "/";
			$category = $fullCategory;
			if($category == "/") {
				$category = 0;
			}
			//$category = str_replace("/", "", $category);
		} else {
			$category = 0;
		}

		return $category;
	}


	/**
	 * @return array
	 */
	public function scanElementsDirectory($path) {

		$files = array();
		$this->_scanFolder($path, $files);

		return $files;
	}


	/**
	 * @param $path
	 * @param $files
	 */
	private function _scanFolder($path, &$files) {

		$directory = new RecursiveDirectoryIterator($path);
		foreach(new RecursiveIteratorIterator($directory) as $filename => $file) {
			$rest = substr($filename, -2);
			if($rest != "/." && $rest != "..") {
				$files[] = $filename;
			}
		}
	}


	/**
	 * @param $file
	 * @return bool
	 */
	public function isElementNotStatic($file, $fileType) {

		$modElementClasses = array(
			"chunks" => "modChunk"
		,"plugins" => "modPlugin"
		,"snippets" => "modSnippet"
		,"templates" => "modTemplate"
		);

		foreach($modElementClasses as $type => $modClass) {
			if($fileType == $type) {
				if(!is_object($this->getStaticElement($file, $modClass))) {
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
	public function getFileType($filePath) {

		$type = array_pop($filePath);
		if($type == "") {
			$type = 0;
		}

		return $type;
	}


	/**
	 * @param $filenameWithSuffix
	 * @return mixed
	 */
	public function getFileName($filenameWithSuffix) {

		$filenameArr = explode(".", $filenameWithSuffix);

		return $filenameArr[0];
	}


	/**
	 * @param $file
	 * @param $modClass
	 * @return null|object
	 */
	public function getStaticElement($file, $modClass) {

		$parameter = array(
			"static" => 1
		,"static_file:LIKE" => "%".$file."%",
		);
		$element = $this->modx->getObject($modClass, $parameter);

		return $element;
	}


	/**
	 * @param $type
	 * @return string
	 */
	public function getElementFieldName($type) {

		if($type == "template") {
			$elementFieldName = "templatename";
		} else {
			$elementFieldName = "name";
		}

		return $elementFieldName;
	}


	/**
	 * @param $category
	 * @return string
	 */
	public function parseCategory($category) {

		$idCategory = "";
		if($category == "0") {
			return "0";
		} else {
			$category = explode("/", $category);
			array_pop($category);
			$parentId = "0";
			for($i = 0; $i < sizeof($category); $i++) {
				$currentCategory = $this->modx->getObject("modCategory", array("category" => $category[$i], "parent" => $parentId));
				if($currentCategory) {
					$idCategory = $currentCategory->id;
					$parentId = $currentCategory->id;

				} else {
					$newCategory = $this->modx->newObject("modCategory");
					$newCategory->set("parent", $parentId);
					$newCategory->set("category", $category[$i]);
					$newCategory->save();
					$parentId = $newCategory->id;
					$idCategory = $newCategory->id;
				}
			}
		}

		return $idCategory;
	}


	/**
	 * @param array $files
	 * @return bool
	 */
	public function createMultipleElements(array $files = array()) {

		if(!$files) {
			$files = $this->getNewFiles();
		}

		$result = false;
		foreach($files as $newFile) {

			$elementData = $this->makeElementDataArray($newFile["category"], $newFile["filename"], $newFile["path"], $newFile["type"], $newFile["mediasource"]);

			$elementObj = $this->modx->newObject($this->modElementClasses[$newFile["type"]][0]);
			$result = $this->setAsStaticElement($elementObj, $elementData, false);
		}

		return $result;
	}


	/**
	 * @param $fileName
	 * @param $filePath
	 * @param $category
	 * @return bool
	 */
	public function createSingleElement($fileName, $filePath, $category) {

		$newFile = $filePath.'/'.$fileName;
		$filePathArray = $this->getFilePathAsArray($newFile);
		$elementType = $this->getFileType($filePathArray);

		$result = false;
		if($this->isElementNotStatic($newFile, $elementType)) {

			$mediaSourceId = $this->modx->getOption("seaccelerator.mediasource", null, true);
			$elementData 	 = $this->makeElementDataArray($category, $fileName, $filePath, $elementType, $mediaSourceId);

			$elementObj = $this->modx->newObject($this->modElementClasses[$elementType][0]);
			$result = $this->setAsStaticElement($elementObj, $elementData, false);
		}

		return $result;
	}


	/**
	 * @param $fileName
	 * @return bool
	 */
	public function deleteFile($fileName, $mediaSource, $path) {

		$file = $this->makeStaticElementFilePath($fileName, $path, $mediaSource, true);

		if($file) {
			unlink($file);
			return true;
		} else {
			return false;
		}
	}


	/**
	 * @param $elementType
	 * @return array
	 */
	public function exportElementsAsStatic($elementType) {

		$modObjectType = $this->modElementClasses[$elementType][0];
		$parameter = array("static" => 0);
		$elements = $this->modx->getCollection($modObjectType, $parameter);

		$result = [];
		$elementsFolder = $this->getElementsFilesystemPath();
		$suffix = $this->getFileSuffix($elementType);

		foreach($elements as $elementObj) {
			$name = $elementObj->get("name");
			$elementData["name"] = $elementObj->get("name");
			$elementData["content"] = $elementObj->get("content");

			if (!empty($elementData["name"]) && !empty($elementData["content"])) {
				$elementData["file"] = $elementsFolder.$elementType."/".$name.$suffix;
				$result = $this->setAsStaticElement($elementObj, $elementData, true);
			}
		}

		return $result;
	}


	/**
	 * @param $elementObj
	 * @param $elementData
	 * @param $isNewFile
	 * @return bool|mixed
	 */
	public function setAsStaticElement($elementObj, $elementData, $isNewFile) {

		$result = false;
		if ($isNewFile) {
			$result = $this->saveElementToFilesystem($elementData);
		}

		if ($result !== false || !$isNewFile) {
			$result = $this->saveElementToDatabase($elementObj, $elementData, true);
		}

		return $result;
	}


	/**
	 * @param $elementObj
	 * @param $elementData
	 * @param $elementType
	 * @return bool|mixed
	 */
	public function unsetAsStaticElement($elementObj, $elementData, $elementType) {

		$result = $this->saveElementToDatabase($elementObj, $elementType, false);

		if ($result) {
			$result = $this->deleteFile($elementData["file"], $elementData["mediaSourceId"], $elementData["path"]);
		}

		return $result;
	}


	/**
	 * @param $elementData
	 * @return bool
	 */
	public function saveElementToFilesystem($elementData) {

		$save = file_put_contents($elementData["file"], $elementData["content"]);
		if ($save !== false) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * @param $elementObj
	 * @param $elementData
	 * @param $static
	 * @return mixed
	 */
	public function saveElementToDatabase($elementObj, $elementData, $static) {

		$elementObj->set($elementData["fieldName"], $elementData["name"]);
		$elementObj->set("source", $elementData["mediaSourceId"]);
		$elementObj->set("static_file", $elementData["staticFile"]);
		$elementObj->set("category", $elementData["categoryId"]);
		$elementObj->set("content", $elementData["content"]);
		$elementObj->set("static", $static);

		$result = $elementObj->save();

		return $result;
	}


	/**
	 * @param $type
	 * @return mixed
	 */
	public function getFileSuffix($type) {

		$fileSuffixes = array(
			"chunks" => ".html",
			"templates" => ".html",
			"snippets" => ".php",
			"plugins" => ".php"
		);

		return $fileSuffixes[$type];
	}


	/**
	 * @param $file
	 * @return bool|string
	 */
	private function getFileContent($file) {

		return file_get_contents($file, true);
	}

	/**
	 * @param $category
	 * @param $fileName
	 * @param $filePath
	 * @param $elementType
	 * @param $mediaSourceId
	 * @return mixed
	 */
	public function makeElementDataArray($category, $fileName, $filePath, $elementType, $mediaSourceId) {

		$elementData["categoryId"] = $this->parseCategory($category);
		$elementData["name"] 			 = $this->getFileName($fileName);
		$elementData["type"]  		 = $this->getModElementClass($elementType);
		$elementData["staticFile"] = $this->makeStaticElementFilePath($fileName, $filePath, $mediaSourceId, false);
		$elementData["file"] 			 = $this->makeStaticElementFilePath($fileName, $filePath, $mediaSourceId, true);
		$elementData["content"] 	 = $this->getFileContent($elementData['file']);
		$elementData["fieldName"]  = $this->getElementFieldName($elementType);

		return $elementData;
	}


	/**
	 * @param $elementType
	 * @return bool
	 */
	private function getModElementClass($elementType) {

		foreach ($this->modElementClasses as $type => $value) {
			//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "modClass: ".$value);
			if ($type == $elementType) {
				return $value[0];
			}
		}

		return false;
	}


}
