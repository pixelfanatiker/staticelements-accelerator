<?php
/**
 * @package seaccelerator
 */
class Seaccelerator {

	public $modx = null;
	public $map = array();
	public $config = array();
	public $typesClass = array(
		"templates" => array("modTemplate"),
		"chunks" => array("modChunk"),
		"snippets" => array("modSnippet"),
		"plugins" => array("modPlugin")
	);
	public $elementsDirectory;


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
	 * @param $source
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
		$this->modx->log(xPDO::LOG_LEVEL_ERROR, "[getNewFiles] elementsPath: ".$elementsPath);
		$filesystem = $this->scanElementsDirectory($elementsPath);

		foreach($filesystem as $file) {
			$this->modx->log(xPDO::LOG_LEVEL_ERROR, "[getNewFiles] file: ".$file);
			$filePathArray = $this->getFilePathAsArray($file);
			$fileName = array_shift($filePathArray);
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

				$this->modx->log(xPDO::LOG_LEVEL_ERROR, "getNewFiles: ".$fileType);

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
				//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "File: " . $filename);
				$files[] = $filename;
			}
		}
	}


	/**
	 * @param $file
	 * @return bool
	 */
	public function isElementNotStatic($file, $fileType) {

		$filePath = $this->getFilePathAsArray($file);

		$fileName = array_reverse(explode(".", array_pop(explode("/", $file))));
		//$filePath = array_reverse(explode("/", str_replace($filePath, "", $file)));

		//$fileExtension = implode(".", array_reverse(array_slice($fileName, 0, $position)));
		//$fileType = $this->getFileType($filePath);

		//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "file: ".$file."; filePath: ".$filePath);

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
	public function removeFileTypeSuffix($filenameWithSuffix) {

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
		//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "parseCategory: ".$category);
		$idCategory = "";
		if($category == "0") {
			return "0";
		} else {
			$category = explode("/", $category);
			array_pop($category);
			$parentId = "0";
			for($i = 0; $i < sizeof($category); $i++) {
				$currentCategory = $this->modx->getObject("modCategory", array("category" => $category[$i], "parent" => $parentId));
				//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "category: ".$category[$i]. "  parent: ".$parentId);
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

		foreach($files as $filesItem) {
			$filePath      = $filesItem["path"];
			$fileName      = $filesItem["filename"];
			$fileType      = $filesItem["type"];
			$mediaSourceId = $filesItem["mediasource"];

			$categoryId    = $this->parseCategory($filesItem["category"]);
			$elementName   = $this->removeFileTypeSuffix($fileName);
			$fieldName     = $this->getElementFieldName($fileType);
			$staticFile    = $this->makeStaticElementFilePath($fileName, $filePath, $mediaSourceId, false);
			$file 				 = $this->makeStaticElementFilePath($fileName, $filePath, $mediaSourceId, true);

			//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "file: ".$file);

			$currentObject = $this->modx->newObject($this->typesClass[$filesItem["type"]][0]);
			$this->setElement($currentObject, $staticFile, $categoryId, $mediaSourceId, $fieldName, $elementName);
			$this->saveElement($currentObject, $file, $fileType);
		}

		return true;
	}


	public function createSingleElement($singleFile, $category) {
		$this->modx->log(xPDO::LOG_LEVEL_DEBUG, "[createSingleElement] singleFile:".$singleFile);

		//$filePathArray = $this->getFilePathAsArray($singleFile);
		//$fileType = $this->getFileType($filePathArray);

		/*if($this->isElementNotStatic($singleFile, $fileType)) {
			$mediaSourceId = $this->modx->getOption("seaccelerator.mediasource", null, true);

			$filePath 	 = array_reverse(explode("/", str_replace($filePathArray, "", $singleFile)));
			$fileName	   = array_shift($filePath);
			$fileType	   = $this->getFileType($singleFile);
			//$category 	 = $this->getElementCategoryFromFilesystem($filePathArray);
			$categoryId  = $this->parseCategory($category);
			$elementPath = $this->makeStaticElementFilePath($fileName, $filePath, $mediaSourceId, false);
			$file 			 = $this->makeStaticElementFilePath($fileName, $filePath, $mediaSourceId, true);
			$elementName = $this->removeFileTypeSuffix(str_replace("." . $this->getFileType($singleFile), "", $fileName));
			$fieldName   = $this->getElementFieldName($fileType);

			$this->modx->log(xPDO::LOG_LEVEL_DEBUG, "[createNewSingleElement] filePath: ".$filePath);

			$currentObject = $this->modx->newObject($fieldName);
			$this->setElement($currentObject, $elementPath, $categoryId, $fieldName, $elementName, $mediaSourceId);
			$this->saveElement($currentObject, $file, $fileType);

			return true;
		} else {
			return false;
		}*/
	}


	/**
	 * @param $currentObject
	 * @param $staticFile
	 * @param $categoryId
	 * @param $mediaSourceId
	 * @param $fieldName
	 * @param $elementName
	 */
	public function setElement($currentObject, $staticFile, $categoryId, $mediaSourceId, $fieldName, $elementName) {

		$currentObject->set($fieldName, $elementName);
		$currentObject->set("static", "1");
		$currentObject->set("source", $mediaSourceId);
		$currentObject->set("static_file", $staticFile);
		$currentObject->set("category", $categoryId);
	}


	/**
	 * @param $currentObject
	 * @param $file
	 * @param $elementType
	 * @return mixed
	 */
	public function saveElement($currentObject, $file, $elementType) {

		$typeArray = array("templates", "snippets", "plugins", "chunks");
		foreach($typeArray as $type) {
			if($elementType == $type) {
				$content = file_get_contents($file, true);
				$currentObject->set("content", $content);
			}
		}

		return $currentObject->save();
	}


	/**
	 * @param $file
	 * @return bool
	 */
	public function deleteFile($file) {

		if($file) {
			unlink($file);
			return true;
		} else {
			return false;
		}
	}


}
