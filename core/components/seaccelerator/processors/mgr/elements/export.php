<?php
/**
 * Export all elements
 *
 * @package seaccelerator
 * @subpackage processors
 */
if (!isset($modx->seaccelerator) || !is_object($modx->seaccelerator)) {
	$seaccelerator = $modx->getService('seaccelerator','Seaccelerator',$modx->getOption('seaccelerator.core_path',null,$modx->getOption('core_path').'components/seaccelerator/').'model/seaccelerator/', $scriptProperties);
	if (!($seaccelerator instanceof Seaccelerator)) return '---';
}

if (!$modx->hasPermission('view')) {
	return $this->failure($modx->lexicon('seaccelerator.no_permission'));
}

$type = $modx->getOption('type', $_REQUEST);
$result = false;

if (!empty($type)) {
	$result = $modx->seaccelerator->exportElementsAsStatic($type."s");
}

return $modx->error->success($result);
