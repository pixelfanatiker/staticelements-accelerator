<?php
/**
 * Sync file
 *
 * @package seaccelerator
 * @subpackage processors
 */
if (!isset($modx->seaccelerator) || !is_object($modx->seaccelerator)) {
  $seaccelerator = $modx->getService('seaccelerator','Seaccelerator',$modx->getOption('seaccelerator.core_path',null,$modx->getOption('core_path').'components/seaccelerator/').'model/seaccelerator/', $scriptProperties);
  if (!($seaccelerator instanceof Seaccelerator)) return '---';
}


$sync = $scriptProperties["sync"];

if ($sync) {

  $elementData['id'] = $scriptProperties["id"];
  $elementData['name'] = $scriptProperties["name"];
  $elementData['source'] = $scriptProperties["source"];
  $elementData['path'] = $scriptProperties["static_file"];
  $elementData['category_id'] = $scriptProperties["category"];
  $elementData['modClass'] = $scriptProperties["modClass"];

  if ($sync == "tofile") {
    $result = $modx->seaccelerator->exportElementAsStatic($elementData);

  } else if ($sync == "fromfile") {
    $result = $modx->seaccelerator->updateChunkFromStaticFile($file, $modClass, $elementData);
  }
}


if ($result) {
  return $modx->error->success("");
} else {
  return $modx->error->failure("");
}
