<?php
/**
 * StaticElements Accelerator
 *
 * Copyright 2016 by Florian Gutwald <florian@frontend-mercenary.com>
 *
 * This file is part of StaticElements Accelerator.
 *
 * StaticElements Accelerator is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * StaticElements Accelerator is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * StaticElements Accelerator; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package seaccelerator
 */
class Seaccelerator {

	public $modx = null;
	public $map = array();
	public $config = array();
  public $elementFileRules;
  public $defaultMediaSource;


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

    $this->elementFileRules = explode(",", $this->modx->getOption("seaccelerator.element_type_rules", null, "modChunk:chunks,modSnippet:snippets,modTemplate:templates,modPlugin:plugins"));
    $this->defaultMediaSource = $this->modx->getOption("seaccelerator.mediasource", null, 1);

		$this->modx->addPackage("seaccelerator",$this->config["modelPath"]);

    if ($this->modx->lexicon) {
      $this->modx->lexicon->load ('seaccelerator:default');
    }
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
	public function getElementsMediaSource() {

		$mediaSourceId = $this->modx->getOption("seaccelerator.mediasource", null, 1);

		return $mediaSourceId;
	}


	/**
	 * @param $mediaSourceId
	 * @return bool
	 */
	public function getMediaSourceName($mediaSourceId) {

		$mediaSource = $this->modx->getObject("sources.modMediaSource", $mediaSourceId);
		if(!empty($mediaSource) && is_object($mediaSource)) {
			return $mediaSource->get("name");
		} else {
			return false;
		}
	}


	/**
	 * @param $results
	 * @return mixed
	 */
	public function getMediaSourceNameFromArray ($results) {

		foreach ($results as $result) {
			$source = $result->get("source");
			$mediaSource = $this->modx->getObject('sources.modMediaSource', $source);
			if(!empty($mediaSource) && is_object($mediaSource)) {
				$mediaSourceName = $mediaSource->get("name");
			} else {
				$mediaSourceName = "None";
			}
			$result->set("mediasource", $mediaSourceName);
		}

		return $results;
	}


  /**
   * @return string
   */
  public function getMediaSources() {

    $mediaSources = $this->modx->getObject('sources.modMediaSource');
    foreach ($mediaSources as $source) {
      $sources[] = array(
          "id" => $source->get("id"),
          "name" => $source->get("name")
      );
    }

    return $this->modx->toJSON($sources);
  }


	/**
	 * @param $elementsPath
	 * @param $mediaSourceId
	 * @return mixed
	 */
	public function getMediaSourcePath($elementsPath, $mediaSourceId) {

		$mediaSource = $this->modx->getObject("sources.modMediaSource", array('id' => $mediaSourceId));
		if(!empty($mediaSource) && is_object($mediaSource)) {
			$elementsPath = $mediaSource->prepareOutputUrl($elementsPath);
		}

		return $elementsPath;
	}


	/**
	 * @param $mediaSourceName
	 * @return int
	 */
	public function getMediaSourceId($mediaSourceName) {

    $c = $this->modx->newQuery('sources.modMediaSource');
    $c->where(array('name' => $mediaSourceName));
    $mediaSources = $this->modx->getCollectionGraph("sources.modMediaSource", $c);

		if(is_object($mediaSources)) {
      $mediaSourceId = $mediaSources->get("id");
		} else {
			$mediaSourceId = 1;
		}

		return $mediaSourceId;
	}


	/**
	 * @return string
	 */
	public function getElementsLocationFilesystemPath() {

		$mediaSourceId = $this->getElementsMediaSource();
		$elementsDirectory = $this->modx->getOption("seaccelerator.elements_directory", null, "elements");
		if($mediaSourceId == 1) {
			$elementsPath = MODX_BASE_PATH . $this->getMediaSourcePath($elementsDirectory, $mediaSourceId);
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

		$actionCreateElement = json_decode('{"className":"check-square-o js_actionLink js_createElement","text":"'. $this->modx->lexicon('seaccelerator.files.actions.create') .'"}');
		$actionEditFile = json_decode('{"className":"edit js_actionLink js_editFile","text":"' .$this->modx->lexicon('seaccelerator.files.actions.edit_file') .'"}');
		$actionDeleteFile = json_decode('{"className":"trash js_actionLink js_deleteFile","text":"' .$this->modx->lexicon('seaccelerator.files.actions.delete_file') .'"}');

		$actions = array($actionCreateElement, $actionEditFile, $actionDeleteFile);

		$newFiles = array();
		$elementsPath = $this->getElementsLocationFilesystemPath();
		$filesystem = $this->scanElementsDirectory($elementsPath);

		foreach($filesystem as $file) {
			$filePathArray = $this->getFilePathAsArray($file);
			$fileName = array_shift ($filePathArray);
			$elementModClass = $this->getElementClass($filePathArray);

			if(!$this->_isElementStatic($fileName, $elementModClass)) {
				$mediaSourceId = $this->getElementsMediaSource();
				$mediaSourceName = $this->getMediaSourceName($mediaSourceId);
				$category = $this->getElementCategoryFromFilesystem($filePathArray);
				$type = $this->_convertModElementClassToType($elementModClass);

				if($mediaSourceId > 0) {
					$filePathString = $this->convertFilePathToString($filePathArray);
				} else {
					$filePathString = $elementsPath.$this->convertFilePathToString($filePathArray);
				}

				$newFiles[] = array(
          "file" => $file,
					"filename" => $fileName,
					"category" => $category,
					"type" => $type,
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

    $filePathString = implode("/", array_reverse(array_filter($filePathArray)));
		if (substr($filePathString, -1, 1) != "/") {
			$filePathString = $filePathString."/";
		}

		return $filePathString;
	}


	/**
	 * @param $fileNameString
	 * @return array
	 */
	public function getFilePathAsArray($fileNameString) {

		$elementsPath = $this->getElementsLocationFilesystemPath();
		$filePath = array_reverse(explode("/", str_replace($elementsPath, "", $fileNameString)));

		return $filePath;
	}


	/**
	 * @param $fileName
	 * @param $file
	 * @param $mediaSource
	 * @param $makeFullPath
	 * @return string
	 */
	public function makeStaticElementFilePath($fileName, $file, $mediaSource, $makeFullPath) {

		if ($fileName == '') {
      //$filePathArray = $this->getFilePathAsArray($file);
      $filePathArray = $this->getFilePathAsArray($file);
      $fileName = array_shift($filePathArray);
      $filePath = $this->convertFilePathToString($filePathArray);
		} else {
			$filePath = $file;
		}

		if (!is_numeric($mediaSource)) {
      $mediaSourceId = $this->getMediaSourceId($mediaSource);
    } else if ($mediaSource == 0) {
      $mediaSourceId = $this->defaultMediaSource;
		} else {
			$mediaSourceId = $mediaSource;
		}

    $elementsPath = $this->modx->getOption("seaccelerator.elements_directory", null, "elements/");
		if($makeFullPath == true) {
      $elementsPath = $this->getMediaSourcePath($filePath, $mediaSourceId);
			$staticElementFilePath = MODX_BASE_PATH.$elementsPath.$fileName;

		} else if($mediaSourceId > 0) {
			$staticElementFilePath = $filePath.$fileName;

		} else {
			$staticElementFilePath = MODX_ASSETS_PATH.$elementsPath.$filePath.$fileName;
		}

    $staticElementFilePath = str_replace("//", "/", $staticElementFilePath);
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


  public function saveStaticElement($elementObject) {

  }


  /**
   * @param $filePathArray
   * @return int|mixed
   */
	public function getElementCategoryFromFilesystem($filePathArray) {

    $filePathArray = array_merge(array_filter($filePathArray));

		$useCategories = $this->modx->getOption("seaccelerator.use_categories", null, true);
		if($useCategories) {
			$fullCategory = array_reverse($filePathArray);
			$category = array_pop($fullCategory);
			//$fullCategory = implode("/", $fullCategory);
			$fullCategory = $fullCategory . "/";
			//$category = $fullCategory;
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
   * @param $filePathArray
   * @return mixed
   */
  public function getElementCategoryPathFromFilesystem($filePathArray) {

    return array_pop($filePathArray);
  }


  /**
   * @param $categoryId
   * @return string
   */
  public function getCategoryName ($categoryId) {

    $categoryName = "";
    if ($categoryId > 0) {
      $categoryObj = $this->modx->getObject('modCategory', $categoryId);
      if (is_object($categoryObj)){
        $categoryName = $categoryObj->get('category');
      }
    }

    return $categoryName;
  }


	/**
	 * @param $path
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
	 * @param $elementModClass
	 * @return bool
	 */
	private function _isElementStatic($file, $elementModClass) {

		if(is_object($this->getStaticElement($file, $elementModClass))) {
			return true;
		} else {
     return false;
    }
	}


  /**
   * @param $filename
   * @return mixed
   */
	public function getElementClass($filename) {

		$elementFileRules = $this->elementFileRules;

    if (is_array($filename)) {
      $elementDirectory = array_pop(array_filter($filename));
    } else {
      $elementDirectory = $filename;
    }

    foreach ($elementFileRules as $rule) {
      $elementRule = explode(":", $rule);
      if ($elementDirectory == $elementRule[1]) {
        return $elementRule[0];
      }
    }
	}


  /**
   * @param $filenameOrModClass
   * @return mixed
   */
  public function getElementDirectory($filenameOrModClass) {

    $elementFileRules = $this->elementFileRules;

    if (is_array($filenameOrModClass)) {
      $elementDirectory = array_pop($filenameOrModClass);
    } else {
      $elementDirectory = $filenameOrModClass;
    }

    foreach ($elementFileRules as $rule) {
      $elementRule = explode(":", $rule);
      if ($elementDirectory == $elementRule[0]) {
        return $elementRule[1];
      } else if ($elementDirectory == $elementRule[1]) {
        return $elementRule[1];
      }
    }
  }


	/**
	 * @param $elementModClass
	 * @return string
	 */
	private function _convertModElementClassToType($elementModClass) {

		return ucfirst(str_replace("mod", "", $elementModClass));
	}


	/**
	 * @param $filenameWithSuffix
	 * @return mixed
	 */
	public function makeElementName($filenameWithSuffix) {

		$filenameArr = explode(".", $filenameWithSuffix);

		return $filenameArr[0];
	}


	/**
	 * @param $file
	 * @param $elementModClass
	 * @return null|object
	 */
	public function getStaticElement($file, $elementModClass) {

		$parameter = array(
			"static" => 1
		,"static_file:LIKE" => "%".$file."%",
		);
		$element = $this->modx->getObject($elementModClass, $parameter);

		return $element;
	}


	/**
	 * @param $type
	 * @return string
	 */
	public function getElementFieldName($type) {

		if($type == "modTemplate" || $type == "template") {
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

		$idCategory = 0;
		if($category == 0) {
			return $idCategory;
		} else {
      if (!is_array($category)) {
        $category = explode("/", $category);
      }

			//array_pop($category);
			$parentId = 0;
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
   * @param $categoryId
   * @return int|string
   */
  public function parseCategoryToPath($categoryId) {

    if ($categoryId == 0) return;

    $categories = $this->modx->getCollection('modCategory');
    $list = [];
    foreach ($categories as $category) {
      $list[$category->id] = array (
        'parent' => $category->parent,
        'name' => $category->category
      );
    }
    $this->_getParents($categoryId, array(), $list);
    $parentsMap = join('/', array_reverse($this->map)) . "/";

    return $parentsMap;
  }


  /**
   * @param $filePathArray
   * @param $elementDirectory
   * @return array
   */
  public function prepareCategories($filePathArray, $elementDirectory) {

    array_shift($filePathArray);
    $separateElementTypeBy = $this->modx->getOption("seaccelerator.element_type_separation", null, "folder");

    $categories = array_reverse($filePathArray);
    if ($separateElementTypeBy == "folder") {
      $elementDirectory = str_replace("/", "", $elementDirectory);
      if ($categories[0] == $elementDirectory) {
        array_shift($categories);
      }
    }

    return $categories;
  }


  /**
   * @param $id
   * @param array $parents
   * @param array $categoryList
   */
  private function _getParents($id, array $parents, array $categoryList) {

    $parents[] = $categoryList[$id]['name'];
    $parent = $categoryList[$id]['parent'];
    if ($parent != 0) {
      $this->_getParents($parent, $parents, $categoryList);
    } else {
      $this->map = $parents;
    }
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
		foreach($files as $file) {

      // TODO: Create method
      $filePathArray = $this->getFilePathAsArray($file['file']);
      $elementModClass = $this->getElementClass($filePathArray);
      $elementDirectory = $this->getElementDirectory($filePathArray);
      $categories = $this->prepareCategories($filePathArray, $elementDirectory);

			$elementData = $this->makeElementDataArray($categories, $file["filename"], $file["path"], $elementModClass, $file["mediasource"]);
			$elementObj  = $this->modx->newObject($elementModClass);
      if (is_object($elementObj)) {
        $result = $this->setAsStaticElement($elementObj, $elementData, false);
      }
		}

		return $result;
	}


	/**
	 * @param $fileName
	 * @param $filePath
	 * @return bool
	 */
	public function createSingleElement($fileName, $filePath) {

    // TODO: Create method
		$file = $filePath.$fileName;
		$filePathArray = $this->getFilePathAsArray($file);
    $elementModClass = $this->getElementClass($filePathArray);
    $elementDirectory = $this->getElementDirectory($filePathArray);
    $categories = $this->prepareCategories($filePathArray, $elementDirectory);

    $isStatic = $this->_isElementStatic($file, $elementModClass);
    $isNewFile = false;
    $result = false;

    if($isStatic == false) {
			$mediaSourceId = $this->modx->getOption("seaccelerator.mediasource", null, true);
			$elementData 	 = $this->makeElementDataArray($categories, $fileName, $filePath, $elementModClass, $mediaSourceId);
			$elementObj    = $this->modx->newObject($elementModClass);
      if (is_object($elementObj)) {
        $result = $this->setAsStaticElement($elementObj, $elementData, $isNewFile);
      }
		}

		return $result;
	}


  /**
   * @param $filePath
   * @return mixed
   */
  public function removeElementDirectory($filePath) {

    $category = str_replace("", "", $filePath);

    return $category;
  }


	/**
	 * @param $staticFile
	 * @param $mediaSource
	 * @return bool
	 */
	public function deleteFile($staticFile, $mediaSource = 1) {

		$file = $this->makeStaticElementFilePath('', $staticFile, $mediaSource, true);

		if($file) {
			unlink($file);
			return true;
		} else {
			return false;
		}
	}


	/**
	 * @param $id
	 * @param $elementModClass
	 * @return bool
	 */
	public function deleteElement($id, $elementModClass) {

		$element = $this->modx->getObject($elementModClass, $id);
		if($element) {
			$result = $element->remove();
		} else {
			$result = false;
		}

		return $result;
	}


	/**
	 * @param $id
	 * @param $staticFile
	 * @param $mediaSource
	 * @param $elementModClass
	 * @return bool
	 */
	public function deleteElementAndFile($id, $staticFile, $mediaSource, $elementModClass) {

		$result = $this->deleteElement($id, $elementModClass);
		if ($result) {
			$result = $this->deleteFile($staticFile, $mediaSource);
		}

		return $result;
	}


  /**
   * @param $elementData
   * @return bool|mixed
   */
  public function exportElementAsStatic($elementData) {

    $parameter = array("id" => $elementData['id']);
    /** @var modElement $elementObj */
    $elementObj = $this->modx->getObject($elementData['modClass'], $parameter);
    if (is_object($elementObj)) {
	  if ( ! $elementObj->isStatic() ) {
	      $elementCategory = $this->parseCategoryToPath($elementData["category_id"]);
	      $elementDirectory = $this->getElementDirectory($elementData['modClass']);
	  
	      $fileSuffix = $this->getFileSuffix($elementData['modClass']);
	      $fileName = $elementData['name'].$fileSuffix;
	      $filePath = $elementDirectory . "/" . $elementCategory;
	      $elementData["static_file"] = $this->makeStaticElementFilePath($fileName, $filePath, $this->defaultMediaSource, false);
	      $elementObj->set('static', true );
	      $elementObj->set('source', $this->defaultMediaSource );
	      $elementObj->set('static_file', $elementData["static_file"] ); // this must be the last one
	      $elementObj->save();
	  }
	  $result = $elementObj->setFileContent( $elementObj->get("content") );

    } else {
      $result = false;
    }

    return $result;
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
		$elementsFolder = $this->getElementsLocationFilesystemPath();
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
   * @param $elementData
   * @return mixed
   */
  public function updateChunkFromStaticFile($elementData) {

    $file = $this->makeStaticElementFilePath($elementData["file"], $elementData["path"], $elementData["source"], true);

    $elementData["content"] = $this->getFileContent($file);
    $elementObj = $this->modx->getObject($elementData["modClass"], $elementData["id"]);

    return $this->saveElementObject($elementObj, $elementData, true);
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
			$result = $this->saveElementObject($elementObj, $elementData, true);
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

		$result = $this->saveElementObject($elementObj, $elementType, false);
		$file = $this->makeStaticElementFilePath($elementData["file"], $elementData["mediaSourceId"], $elementData["path"], true);

		if ($result) {
			$result = $this->deleteFile($file);
		}

		return $result;
	}


	/**
	 * @param $elementData
	 * @return bool
	 */
	public function saveElementToFilesystem($elementData) {

    $result = file_put_contents($elementData["file"], $elementData["content"]);
		if ($result === false) {
      $parts = explode('/', $elementData["file"]);
      $file = array_pop($parts);
      if ($parts[0] == "") {
        array_shift($part);
      };
      $dir = '';
      foreach($parts as $part) {
        if (!is_dir($dir .= "/$part")) mkdir($dir, 0644);
      }
      $result = file_put_contents("$dir/$file", $elementData["content"]);
      if ($result !== false){
        $result = true;
      }
		} else {
      $result = true;
		}
    return $result;
	}


	/**
	 * @param $elementObj
	 * @param $elementData
	 * @param $static
	 * @return mixed
	 */
	public function saveElementObject($elementObj, $elementData, $static) {

    $fieldName = $this->getElementFieldName($elementData["type"]);

		$elementObj->set($fieldName, $elementData["name"]);
		$elementObj->set("source", $elementData["source"]);
		$elementObj->set("static_file", $elementData["static_file"]);
		$elementObj->set("category", $elementData["categoryId"]);
		$elementObj->set("content", $elementData["content"]);
		$elementObj->set("static", $static);

		$result = $elementObj->save();

		return $result;
	}


  /**
   * @param $elementObj
   * @param $elementRecord
   * @return mixed
   */
  public function updateElement($elementObj, $elementRecord) {

    $elementData['name'] = $elementRecord['name'];
    $elementData['source'] = $elementRecord['source'];
    $elementData['static_file'] = $elementRecord['static_file'];
    $elementData['category'] = $elementRecord['category'];
    $elementData['content'] = $elementRecord['content'];
    //$elementData['description'] = $elementRecord['description'];

    $result = $this->saveElementObject($elementObj, $elementData, true);

    return $result;
  }


	/**
	 * @param $type
	 * @return mixed
	 */
	public function getFileSuffix($type) {

		if (strpos($type, "mod") !== false) {
			$fileSuffixes = array(
				"modChunk" => ".html",
				"modTemplate" => ".html",
				"modSnippet" => ".php",
				"modPlugin" => ".php"
			);

		} else {
			$fileSuffixes = array(
				"chunks" => ".html",
				"templates" => ".html",
				"snippets" => ".php",
				"plugins" => ".php"
			);
		}

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
	 * @param $file
	 * @return string
	 */
	public function getFileContentAsSHA1($file) {
		return sha1_file($file);
	}


	/**
	 * @param $file
	 * @return string
	 */
	private function checkElementOnFilesystem($file) {

		if (file_exists($file)) {
			$content = $this->getFileContentAsSHA1($file);
		} else {
			$content = "";
		}

		return $content;
	}


	/**
	 * @param $category
	 * @param $fileName
	 * @param $filePath
	 * @param $elementModClass
	 * @param $mediaSourceId
	 * @return mixed
	 */
	public function makeElementDataArray($category, $fileName, $filePath, $elementModClass, $mediaSourceId) {

		$elementData["categoryId"]  = $this->parseCategory($category);
		$elementData["name"] 			  = $this->makeElementName($fileName);
    $elementData["static_file"] = $this->makeStaticElementFilePath($fileName, $filePath, $mediaSourceId, false);
    $elementData["file"] 			  = $this->makeStaticElementFilePath($fileName, $filePath, $mediaSourceId, true);
    $elementData["content"] 	  = $this->getFileContent($elementData['file']);
    $elementData["type"]  		  = $elementModClass;
    $elementData["source"]      = $mediaSourceId;

    return $elementData;
	}


	/**
	 * @param $results
	 * @return mixed
	 */
	public function prepareElementGridData($results, $classKey) {

		foreach ($results as $result) {
			$name = $result->get('name');
			$filename = $this->makeFilename($name, $classKey);

			$elementData['name']  			= $name;
			$elementData['filename']		= $filename;
			$elementData['content']  		= $result->get('content');
			$elementData['static_file'] = $result->get('static_file');
			$elementData['static']  		= $result->get('static');
			$elementData['source'] 			= $result->get('source');
      //$elementData['category'] 		= $result->get('category');
      $elementData['classKey'] 		= $classKey;

      $result->set('category_name', $this->getCategoryName($result->get('category')));
			$result->set('status', $this->getElementStatusIcon($elementData));
			$result->set('actions', $this->getElementActionIcons($elementData));
		}

		return $results;
	}


	/**
	 * @param $name
	 * @param $classKey
	 * @return string
	 */
	public function makeFilename($name, $classKey) {

		$suffix = $this->getFileSuffix($classKey);

		return $name.$suffix;
	}


	/**
	 * @param $elementData
	 * @return array
	 */
	public function checkElementStatus($elementData) {

		$status['path'] 	 = $elementData['path'];
		$status['static']  = $elementData['static'];
		$status['deleted'] = false;
		$status['changed'] = false;

		// Element has an path and is static
		if ($elementData['static'] == true && $elementData['static_file'] != "") {

			$path = str_replace($elementData['filename'], "", $elementData['static_file']);
			$file = $this->makeStaticElementFilePath($elementData['filename'], $path, $elementData['source'], true);

			$elementContentFilesystem = $this->checkElementOnFilesystem($file);
			$elementContentDatabase = sha1($elementData['content']);

			// Is element existing on filesystem?
			if ($elementContentFilesystem == "") {
				$status['deleted'] = true;
			}

			// Has content changed?
			if ($elementContentDatabase != $elementContentFilesystem) {
				$status['changed'] = true;
			}
		}

		$elementStatus = $this->setElementStatusAndAction($status);

		return $elementStatus;
	}


	/**
	 * @param $status
	 * @return array
	 */
	public function setElementStatusAndAction($status) {

		$statusAndActions = [];

		if ($status['deleted'] == true) {
			$statusAndActions['status'] = "deleted";
			$statusAndActions['action'] = array("editElement", "syncToFile", "syncFromFileDisabled", "deleteElement", "deleteBothDisabled");

		} else if ($status['static'] == false) {
			$statusAndActions['status'] = "static";
			$statusAndActions['action'] = array("editElement", "syncToFile", "syncFromFileDisabled", "deleteElement", "deleteBothDisabled");

		} else if ($status['deleted'] == false && $status['changed'] == true) {
			$statusAndActions['status'] = "changed";
			$statusAndActions['action'] = array("editElement", "syncToFile", "syncFromFile", "deleteElement", "deleteBoth");

		} else if ($status['deleted'] == false && $status['changed'] == false) {
			$statusAndActions['status'] = "unchanged";
			$statusAndActions['action'] = array("editElement", "syncToFileDisabled", "syncFromFileDisabled", "deleteElement", "deleteBoth");
		}

		return $statusAndActions;
	}


	/**
	 * @param $elementData
	 * @return mixed
	 */
	public function getElementStatusIcon($elementData) {

		$statusAndAction = $this->checkElementStatus($elementData);

		$statusIconRepository = array(
			'changed' => '{"className":"exclamation-circle sm-orange","text":"'. $this->modx->lexicon('seaccelerator.elements.element_status.changed') .'"}',
			'unchanged' => '{"className":"check-circle sm-green","text":"'. $this->modx->lexicon('seaccelerator.elements.element_status.unchanged') .'"}',
			'deleted' => '{"className":"warning sm-red","text":"'. $this->modx->lexicon('seaccelerator.elements.element_status.deleted') .'"}',
			'static' => '{"className":"info-circle sm-orange","text":"'. $this->modx->lexicon('seaccelerator.elements.element_status.not_static') .'"}'
		);

		return json_decode($statusIconRepository[$statusAndAction['status']]);
	}


	/**
	 * @param $elementData
	 * @return array
	 */
	public function getElementActionIcons($elementData) {

		$statusAndActions = $this->checkElementStatus($elementData);
		$actions = $statusAndActions['action'];
		$actionIcons = [];

		$actionIconsRepository = array(
			'editElement' =>   				 '{"className":"edit js_actionLink js_editElement","text":"'. $this->modx->lexicon('seaccelerator.elements.actions.quickupdate') .'"}',
			'syncToFile' =>  	 				 '{"className":"arrow-circle-o-down js_actionLink js_syncToFile","text":"'. $this->modx->lexicon('seaccelerator.elements.actions.sync.tofile') .'"}',
			'syncToFileDisabled' =>		 '{"className":"arrow-circle-o-down disabled","text":"'. $this->modx->lexicon('seaccelerator.elements.actionss.sync.tofile') .'"}',
			'syncFromFile' =>	 				 '{"className":"arrow-circle-o-up js_actionLink js_syncFromFile","text":"'. $this->modx->lexicon('seaccelerator.elements.actions.sync.fromfile') .'"}',
			'syncFromFileDisbabled' => '{"className":"arrow-circle-o-up disabled","text":"'. $this->modx->lexicon('seaccelerator.elements.actionss.sync.fromfile') .'"}',
			'deleteElement' => 				 '{"className":"minus-square-o js_actionLink js_deleteElement","text":"'. $this->modx->lexicon('seaccelerator.elements.actions.delete') .'"}',
			'deleteBoth' => 	 				 '{"className":"trash js_actionLink js_deleteFileElement","text":"'. $this->modx->lexicon('seaccelerator.elements.actions.delete_file_element') .'"}',
			'deleteBothDisabeld' => 	 '{"className":"trash disabled","text":"'. $this->modx->lexicon('seaccelerator.elements.actions.delete_file_element') .'"}',
		);

		foreach ($actionIconsRepository as $actionIcon => $actionIconContent) {
			foreach ($actions as $action) {
				if ($action == $actionIcon) {
					$actionIcons[] = json_decode($actionIconContent);
				}
			}
		}

		return $actionIcons;
	}


  /**
   * @param $results
   * @return mixed
   */
  public function addCategoryName($results) {

    foreach ($results as $result) {
      $categoryId = $result->get('category');
      $this->modx->log(xPDO::LOG_LEVEL_DEBUG, $categoryId);
      //$category = $this->getCategoryName($categoryId);
      //$result->set('category', $category);
    }

    return $results;
  }


}
