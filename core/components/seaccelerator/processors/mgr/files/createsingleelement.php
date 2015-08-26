<?php
/**
 * CreateSingleElementProcessor
 *
 * @package seaccelerator
 * @subpackage processors
 */

$filename = $scriptProperties['filename'];
$path = $scriptProperties['path'];
$category = $scriptProperties['category'];

$modx->log(xPDO::LOG_LEVEL_DEBUG, "[CreateSingleElementProcessor] filename:".$filename);


if (!isset($modx->seaccelerator) || !is_object($modx->seaccelerator)) {
	$seaccelerator = $modx->getService('seaccelerator','Seaccelerator',$modx->getOption('seaccelerator.core_path',null,$modx->getOption('core_path').'components/seaccelerator/').'model/seaccelerator/', $scriptProperties);
	if (!($seaccelerator instanceof Seaccelerator)) return '---';
}

if (!$modx->hasPermission('view')) {
	return $this->failure($modx->lexicon('seaccelerator.no_permission'));
}

$filename = $scriptProperties['filename'];
$path = $scriptProperties['path'];
$category = $scriptProperties['category'];

$this->modx->log(xPDO::LOG_LEVEL_DEBUG, "[CreateSingleElementProcessor] filename:".$filename);

//$result = $modx->seaccelerator->createSingleElement($path.'/'.$filename, $category);

//return $modx->error->success($result);
