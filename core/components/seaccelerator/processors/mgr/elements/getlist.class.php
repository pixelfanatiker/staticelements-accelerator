<?php
/**
 * Class modSeacceleratorGetListOfElementsProcessor
 *
 * @package seaccelerator
 * @subpackage processors
 */
class modSeacceleratorGetListOfElementsProcessor extends modObjectGetListProcessor {

	public $seaccelerator = null;
	public $defaultSortField = 'name';

	/**
	 * @return array
	 */
	public function getData() {

		$this->modx->loadClass("seaccelerator.seaccelerator");
		$this->seaccelerator = new Seaccelerator($this->modx);

		$this->modx->getService('lexicon','modLexicon');
		$this->modx->lexicon->load ('seaccelerator:default');

		$data = array();

		$nameFilter = $this->getProperty('namefilter');
		$categoryFilter = $this->getProperty('categoryfilter');

		$type = $this->getProperty('type');
		$this->classKey = 'mod' . ucfirst($type);

		$limit = intval($this->getProperty('limit'));
		$start = intval($this->getProperty('start'));

		$query = $this->modx->newQuery($this->classKey);

		if (!empty($nameFilter)) {
			$key_filter = ($this->classKey == 'modTemplate') ? 'templatename' : 'name';
			$query->where(array($key_filter . ':LIKE' => '%' . $nameFilter . '%'));
		}

		if (!empty($categoryFilter)) {
			$query->where(array('category' => $categoryFilter));
		}

		$query = $this->prepareQueryBeforeCount($query);
		$data['total'] = $this->modx->getCount($this->classKey, $query);
		$query = $this->prepareQueryAfterCount($query);

		$sortField = $this->getProperty('sort');
		$sortField = ($sortField == 'name' and $this->classKey == 'modTemplate') ? 'templatename' : 'name';

		$sortClassKey = $this->getSortClassKey();
		$sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '', array($sortField));
		if (empty($sortKey)) $sortKey = $sortField;
		//$c->sortby($sortKey,$this->getProperty('dir'));

		$query->sortby('static', 'ASC');

		if ($limit > 0) {
			$query->limit($limit, $start);
		}

		$data['results'] = $this->modx->getCollection($this->classKey, $query);
		$data['results'] = $this->isElementChanged($data['results']);
		$data['results'] = $this->addMediaSourceName($data['results']);

		return $data;
	}

	/**
	 * @param xPDOObject $object
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		return $object->toArray();
	}


	/**
	 * @param $results
	 * @return mixed
	 */
	public function addMediaSourceName ($results) {

		foreach ($results as $result) {
			$source = $result->get("source");
			$mediaSource = $this->modx->getObject('sources.modMediaSource', $source);
			if(!empty($mediaSource) && is_object($mediaSource)) {
				$mediaSourceName = $mediaSource->get("name");
			} else {
				$mediaSourceName = "None";
			}
			$result->set("mediasource", $mediaSourceName);
			$this->modx->log(xPDO::LOG_LEVEL_ERROR, "mediasource: ".$mediaSourceName);
		}
		return $results;
	}

	/**
	 * @param $results
	 * @return mixed
	 */
	public function isElementChanged($results) {

		foreach ($results as $result) {
			$content = sha1($result->get('content'));

			$staticFile = $result->get('static_file');
			$mediaSource = $result->get('mediasource');
			$static = $result->get('static');


			if (!file_exists($staticFile)) {
				$contentNew = "File not found";
			} else {
				$contentNew = sha1_file($staticFile);
			}

			// TODO: Refactoring for better handling
			$actionEditElement = json_decode('{"className":"edit js_actionLink js_editElement","text":"'. $this->modx->lexicon('seaccelerator.common.actions.element.quickupdate') .'"}');
			$actionSyncToFile = json_decode('{"className":"arrow-circle-o-down js_actionLink js_syncToFile","text":"'. $this->modx->lexicon('seaccelerator.common.actions.elements.sync.tofile') .'"}');
			$actionRestoreToFile = json_decode('{"className":"arrow-circle-o-down js_actionLink js_restoreToFile","text":"'. $this->modx->lexicon('seaccelerator.common.actions.elements.restore.tofile') .'"}');
			$actionSyncFromFile = json_decode('{"className":"arrow-circle-o-up js_actionLink js_syncFromFile","text":"'. $this->modx->lexicon('seaccelerator.common.actions.elements.sync.fromfile') .'"}');
			$actionExportToFile = json_decode('{"className":"save js_actionLink js_exportToFile","text":"'. $this->modx->lexicon('seaccelerator.common.actions.element.static') .'"}');
			$actionDeleteElement = json_decode('{"className":"minus-square-o js_actionLink js_deleteElement","text":"'. $this->modx->lexicon('seaccelerator.common.actions.element.delete') .'"}');
			$actionDeleteFileElement = json_decode('{"className":"trash js_actionLink js_deleteFileElement","text":"'. $this->modx->lexicon('seaccelerator.common.actions.element.deletefile_element') .'"}');

			$actionDeleteFileElementDisabled = json_decode('{"className":"trash disabled","text":"Delete file and element"}');
			$actionDeleteElementDisabled = json_decode('{"className":"minus-square-o disabled","text":"Delete element"}');
			$actionEditElementDisabled = json_decode('{"className":"edit disabled","text":"Edit element"}');

			$actionSyncToFileDisabled = json_decode('{"className":"arrow-circle-o-down disabled","text":"'. $this->modx->lexicon('seaccelerator.common.actions.elements.sync.tofile') .'"}');
			$actionSyncFromFileDisabled = json_decode('{"className":"arrow-circle-o-up disabled","text":"'. $this->modx->lexicon('seaccelerator.common.actions.elements.sync.fromfile') .'"}');

			$actionExportToFileDisabled = json_decode('{"className":"save disabled","text":"'. $this->modx->lexicon('seaccelerator.common.actions.element.static') .'"}');

			$statusUnchanged = json_decode('{"className":"check-circle sm-green","text":"'. $this->modx->lexicon('seaccelerator.elements.status.unchanged') .'"}');
			$statusChanged = json_decode('{"className":"exclamation-circle sm-orange","text":"'. $this->modx->lexicon('seaccelerator.elements.status.changed') .'"}');
			$statusDeleted = json_decode('{"className":"warning sm-red","text":"'. $this->modx->lexicon('seaccelerator.elements.status.deleted') .'"}');

			if ($static == false && $contentNew == "File not found") {
				$varActionSaveElement = $actionRestoreToFile;
				$varActionExportElement = $actionExportToFileDisabled;
			} else if ($static == true && $contentNew == "File not found") {
				$varActionSaveElement = $actionRestoreToFile;
				$varActionExportElement = $actionExportToFileDisabled;
			} else if ($static == true) {
				$varActionSaveElement = $actionSyncToFileDisabled;
				$varActionExportElement = $actionExportToFileDisabled;
			}

			if ($contentNew == "File not found") {
				$result->set('status', $statusDeleted);
				$result->set('actions', array(
					$actionEditElement,
					$varActionSaveElement,
					$actionSyncFromFileDisabled,
					$varActionExportElement,
					$actionDeleteElement,
					$actionDeleteFileElementDisabled
				));

			} else {
				if ($content != $contentNew) {
					$result->set('status', $statusChanged);
					$result->set('actions', array(
						$actionEditElement,
						$actionSyncToFile,
						$actionSyncFromFile,
						$varActionExportElement,
						$actionDeleteElement,
						$actionDeleteFileElement
					));
				} else {
					$result->set('status', $statusUnchanged);
					$result->set('actions', array(
						$actionEditElement,
						$actionSyncToFileDisabled,
						$actionSyncFromFileDisabled,
						$varActionExportElement,
						$actionDeleteElement,
						$actionDeleteFileElement
					));
				}
			}

			// TODO: Optimize Status
			//$fileName = array_reverse ($file)[0];
			//$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] [getData] : ' . $fileName);
		}
		return $results;
	}
}

return 'modSeacceleratorGetListOfElementsProcessor';
