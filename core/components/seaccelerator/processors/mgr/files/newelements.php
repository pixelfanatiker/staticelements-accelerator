<?php

$modx->log(xPDO::LOG_LEVEL_ERROR, "newelements");

$this->modx->loadClass("seaccelerator.seaccelerator");
$this->seaccelerator = new Seaccelerator($this->modx);

$basePath = $modx->getOption('seaccelerator.core_path',null,$modx->getOption('core_path').'components/seaccelerator/');

$modx->log(xPDO::LOG_LEVEL_ERROR, "newelements 1: " .$basePath);

$seaccelerator = $modx->getService('seaccelerator','Seaccelerator',$basePath.'model/seaccelerator/', $scriptProperties);
if (!($seaccelerator instanceof Seaccelerator)) return '---';

if (!isset($modx->seaccelerator) || !is_object($modx->seaccelerator)) {

} else {
	$modx->log(xPDO::LOG_LEVEL_ERROR, "newelements 2");
}

if (!$modx->hasPermission('view')) {
	return $this->failure($modx->lexicon('seaccelerator.no_permission'));
}

$kk = $modx->seaccelerator->createNewElements();

return $modx->error->success($kk);
