<?php
/**
 * newElementsProcessor
 *
 * @package seaccelerator
 * @subpackage processors
 */
if (!isset($modx->seaccelerator) || !is_object($modx->seaccelerator)) {
	$seaccelerator = $modx->getService('seaccelerator','SEManager',$modx->getOption('seaccelerator.core_path',null,$modx->getOption('core_path').'components/seaccelerator/').'model/seaccelerator/', $scriptProperties);
	if (!($seaccelerator instanceof SEManager)) return '---';
}

$file = $scriptProperties["file"];

$result = true;

if ($file) {
	$result = $modx->seaccelerator->deleteFile($file);
} else {
	$result = false;
}


if ($result == true) {
	return $modx->error->success("");
} else {
	return $modx->error->failure("");
}
