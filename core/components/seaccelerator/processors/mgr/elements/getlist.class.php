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
		$data['results'] = $this->seaccelerator->getElementStatusAndActions($data['results'], $this->classKey);
		$data['results'] = $this->seaccelerator->getMediaSourceNameFromArray($data['results']);

		return $data;
	}


	/**
	 * @param xPDOObject $object
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		return $object->toArray();
	}


}

return 'modSeacceleratorGetListOfElementsProcessor';
