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

$process 		 = $modx->getOption('process', $_REQUEST);
$id 	 			 = $modx->getOption('id', $_REQUEST);
$type				 = $modx->getOption('type', $_REQUEST);
$mediaSource = $modx->getOption('source', $_REQUEST);
$staticFile	 = $modx->getOption('staticfile', $_REQUEST);

if ($process == "element") {
	$result = $modx->seaccelerator->deleteFile($staticFile, $mediaSource);

} else if ($process == "both") {
	$result = $modx->seaccelerator->deleteElementAndFile($id, $staticFile, $mediaSource, $type.'s');

} else {
	$result = false;
}


if ($result == true) {
	return $modx->error->success("");
} else {
	return $modx->error->failure("");
}
