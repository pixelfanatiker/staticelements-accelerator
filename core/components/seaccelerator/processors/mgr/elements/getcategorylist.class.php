<?php
/**
 * Class modSeacceleratorGetCategoryListProcessor
 *
 *  * @package nominations
 * @subpackage processors
 */
class modSeacceleratorGetCategoryListProcessor extends modObjectGetListProcessor {

	public $classKey = "modCategory";
	public $defaultSortField = "category";

	function getData() {
		$data = array();

		$limit = intval($this->getProperty("limit"));
		$start = intval($this->getProperty("start"));

		$query = $this->modx->newQuery($this->classKey);
		$query = $this->prepareQueryBeforeCount($query);
		$query = $this->prepareQueryAfterCount($query);
		$data["total"] = $this->modx->getCount($this->classKey, $query);

		$sortClassKey = $this->getSortClassKey();
		$sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty("sortAlias",$sortClassKey), "", array($this->getProperty("sort")));

		if (empty($sortKey)) $sortKey = $this->getProperty("sort");
		$query->sortby($sortKey,$this->getProperty("dir"));

		if ($limit > 0) {
			$query->limit($limit,$start);
		}
		$query->select("id,category");

		$data["results"] = $this->modx->getCollection($this->classKey, $query);

		return $data;
	}
}

return "modSeacceleratorGetCategoryListProcessor";
