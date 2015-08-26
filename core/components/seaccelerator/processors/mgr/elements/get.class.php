<?php
/**
 * Get a list of Seaccelerator
 *
 * @package nominations
 * @subpackage processors
 */
class SeacceleratorGetNominationProcessor extends modObjectGetListProcessor {
	public $classKey = 'Seaccelerator';
	public $languageTopics = array('seaccelerator:default');
	public $defaultSortField = 'id';
	public $defaultSortDirection = 'ASC';


	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$query = $this->getProperty('query');
		if (!empty($query)) {
			$c->where(array(
				'id' => $query
			));
		}

		/*foreach ($c as $row) {
     $this->modx->log(xPDO::LOG_LEVEL_ERROR, $row);
     }*/

		return $c;
	}
}
return 'SeacceleratorGetNominationProcessor';
