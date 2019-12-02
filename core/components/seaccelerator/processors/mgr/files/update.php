<?php
/**
 * Edit file
 *
 * @package seaccelerator
 * @subpackage processors
 */
if (!isset($modx->seaccelerator) || !is_object($modx->seaccelerator)) {
	$seaccelerator = $modx->getService('seaccelerator','Seaccelerator',$modx->getOption('seaccelerator.core_path',null,$modx->getOption('core_path').'components/seaccelerator/').'model/seaccelerator/', $scriptProperties);
	if (!($seaccelerator instanceof Seaccelerator)) return '---';
}

$file 	 = $modx->getOption('file', $_REQUEST);
$mediaSource = $modx->getOption('mediasource', $_REQUEST);
$content 		 = $modx->getOption('content', $_REQUEST);

$result = false;
if($file){
	$file = $modx->seaccelerator->makeStaticElementFilePath('', $file, $mediaSource, true);
	
	$result = file_put_contents ($file, $content);
}

if (false !== $result) {
	return $modx->error->success("");
} else {
	return $modx->error->failure("");
}
