<?php
/**
 * Get list of files
 *
 * @package seaccelerator
 * @subpackage processors
 */
class FilesGetListProcessor extends modObjectGetListProcessor {

	public $seaccelerator = null;
	public $languageTopics = array('seaccelerator:default');
	public $defaultSortField = 'name';
	public $defaultSortDirection = 'ASC';


	/**
	 * @return array
	 */
	public function getFiles() {
		//$result = array();

		$this->modx->loadClass("seaccelerator.seaccelerator");
		$this->seaccelerator = new Seaccelerator($this->modx);

		$result = $this->getFiles();

		return $result;
	}
}
return 'FilesGetListProcessor';
