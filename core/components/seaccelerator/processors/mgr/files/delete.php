<?php
/**
 * Delete File
 *
 * @package seaccelerator
 * @subpackage processors
 */
if (!isset($modx->seaccelerator) || !is_object($modx->seaccelerator)) {
	$seaccelerator = $modx->getService('seaccelerator','Seaccelerator',$modx->getOption('seaccelerator.core_path',null,$modx->getOption('core_path').'components/seaccelerator/').'model/seaccelerator/', $scriptProperties);
	if (!($seaccelerator instanceof Seaccelerator)) return '---';
}

if (!$modx->hasPermission('delete')) {
	return $this->failure($modx->lexicon('seaccelerator.no_permission.delete'));
}

$fileName 	 = $modx->getOption('filename', $_REQUEST);
$mediaSource = $modx->getOption('mediasource', $_REQUEST);
$path 			 = $modx->getOption('path', $_REQUEST);

$result = true;

if ($fileName && $path) {
	$result = $modx->seaccelerator->deleteFile($fileName, $mediaSource, $path);
} else {
	$result = false;
}


if ($result == true) {
	return $modx->error->success("");
} else {
	return $modx->error->failure("");
}
