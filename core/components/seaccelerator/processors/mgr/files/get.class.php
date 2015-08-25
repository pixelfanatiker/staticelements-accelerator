<?php
/**
 * Get a list of Nominations
 *
 * @package nominations
 * @subpackage processors
 */
class NominationsGetNominationProcessor extends modObjectGetListProcessor {
	public $classKey = 'Nomination';
	public $languageTopics = array('nominations:default');
	public $defaultSortField = 'id';
	public $defaultSortDirection = 'ASC';
	public $objectType = 'nominations.nominations';


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
return 'NominationsGetNominationProcessor';