<?php
require_once (dirname(__FILE__).'/update.class.php');
/**
 * @package Seaccelerator
 * @subpackage processors
 */
class SeacceleratorUpdateFromGridProcessor extends SeacceleratorUpdateProcessor {

  public $seaccelerator;

  public function checkPermissions() {
    return true;
  }

  public function getLanguageTopics() {
    return array('seaccelerator');
  }


  /**
   * @return array|null|string
   */
  public function process() {

    $data = $this->getProperty('data');
    if (empty($data)) return $this->modx->lexicon('seaccelerator.error.ufg_no_data');

    $this->modx->loadClass("seaccelerator.seaccelerator");
    $this->seaccelerator = new Seaccelerator($this->modx);

    $elementRecord = $this->modx->fromJSON($data);
    $elementObject = $this->modx->getObject($elementRecord['modClass'], array('id' => $elementRecord['id']));

    $this->modx->log(xPDO::LOG_LEVEL_ERROR, "SeacceleratorUpdateFromGridProcessor id:" . $elementRecord['id']);

    /*if (is_object($elementObject)) {
      $result = $this->seaccelerator->updateElement($elementObject, $elementRecord);
    } else {
      $result = false;
    }

    if ($result) {
      return $this->modx->error->success("");
    } else {
      return $this->modx->error->failure("");
    }*/

    return $this->modx->error->success("");
  }

}

return 'SeacceleratorUpdateFromGridProcessor';
