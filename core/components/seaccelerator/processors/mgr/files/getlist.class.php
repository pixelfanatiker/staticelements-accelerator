<?php
/**
 * Get list of files
 *
 * @package seaccelerator
 * @subpackage processors
 */
class modGetListOfFilesProcessor extends modObjectGetListProcessor {

	public $seaccelerator = null;
	public $languageTopics = array('seaccelerator:default');
	public $defaultSortField = 'name';
	public $defaultSortDirection = 'ASC';


	/**
	 * @return array
	 */
	public function getData() {
		$response = array();
		$files = array();

		$start = $this->modx->getOption('start', $_REQUEST, 0);
		$limit = $this->modx->getOption('limit', $_REQUEST, 20);
		$namefilter = $this->modx->getOption('namefilter', $_REQUEST, '');
		$typefilter = $this->modx->getOption('type', $_REQUEST, '');

		$this->modx->loadClass("seaccelerator.seaccelerator");
		$this->seaccelerator = new Seaccelerator($this->modx);

		$files['all'] = $this->seaccelerator->getNewFiles();
		$count = 0;
		if ($namefilter) {
			for ($i = 0; $i < sizeof($files['all']); $i++) {
				if (strpos($files['all'][$i]['filename'], $namefilter) !== false) {
					$count++;
					$response['results_step'][] = $files['all'][$i];
				}
			}
			for ($i = $start; $i < $limit + $start; $i++) {
				$response['results'][] = $response['results_step'][$i];
			}
			$response['total'] = $count;

		} else if ($typefilter) {
			for ($i = 0; $i < sizeof($files['all']); $i++) {
				if ($files['all'][$i]['type'] == $typefilter) {
					$count++;
					$response['results_step'][] = $files['all'][$i];
				}
			}
			for ($i = $start; $i < $limit + $start; $i++) {
				$response['results'][] = $response['results_step'][$i];
			}
			$response['total'] = $count;

		} else {
			for ($i = $start; $i < $limit + $start; $i++) {
				$response['results'][$i] = $files['all'][$i];
				//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "results: ".$response['results'][$i]);
			}
			$response['total'] = count($files['all']);
			//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "total: ".$result['total']);
		}
		$this->modx->log(xPDO::LOG_LEVEL_ERROR, "total: ".$response['total'].'  '.$response['results'][0]);
		return $response;
	}


	/**
	 * @param xPDOObject $object
	 * @return xPDOObject
	 */
	public function prepareRow($object) {
		return $object;
	}
}

return 'modGetListOfFilesProcessor';
