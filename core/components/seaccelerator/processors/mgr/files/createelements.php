<?php
/**
 * CreateMultipleElementsProcessor
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

$process 	= $modx->getOption('process', $_REQUEST);
$filename = $modx->getOption('filename', $_REQUEST);
$category = $modx->getOption('category', $_REQUEST);
$path 		= $modx->getOption('path', $_REQUEST);

if ($process == "multi") {
	$result = $modx->seaccelerator->createMultipleElements();
} else {
	$result = $modx->seaccelerator->createSingleElement($filename, $path, $category);
}

//$modx->error->addError("test");
return $modx->error->success($result);
