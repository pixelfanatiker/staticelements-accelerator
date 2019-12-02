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

$fileName 	 = $modx->getOption('filename', $_REQUEST);
$mediaSource = $modx->getOption('mediasource', $_REQUEST);
$path 			 = $modx->getOption('path', $_REQUEST);
$content 		 = $modx->getOption('content', $_REQUEST);


if($fileName && $path){
	$file = $seaccelerator->makeStaticElementFilePath($fileName, $path, $mediaSource, true);

	file_put_contents ($file, $content);

	return $modx->error->success('',$item);
}
