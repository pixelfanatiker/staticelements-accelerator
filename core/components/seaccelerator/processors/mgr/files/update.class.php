<?php
/**
 * @package nomination
 * @subpackage processors
 */
class NominationsUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'Nomination';
	public $languageTopics = array('nominations:default');
	public $objectType = 'nominations.nominations';

	public function beforeSet() {
		$this->handleComboBoolean('nomination_status');

		return parent::beforeSet();
	}

	public function handleComboBoolean($property) {
		$boolean = $this->getProperty($property);

		if ($boolean == 'true') {
			$this->setProperty($property, true);
			return true;
		}

		if ($boolean == 'false') {
			$this->setProperty($property, false);
			return false;
		}
		$this->setProperty($property, null);

		return null;
	}
}
return 'NominationsUpdateProcessor';