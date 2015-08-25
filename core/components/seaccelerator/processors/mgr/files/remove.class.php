<?php
/**
 * @package nomination
 * @subpackage processors
 */
class NominationsRemoveProcessor extends modObjectRemoveProcessor {
	public $classKey = 'Nomination';
	public $languageTopics = array('nominations:default');
	public $objectType = 'nominations.nominations';
}
return 'NominationsRemoveProcessor';