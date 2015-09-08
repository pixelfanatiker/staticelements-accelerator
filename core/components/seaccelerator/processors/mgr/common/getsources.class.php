<?php
/**
 * Get list of files
 *
 * @package seaccelerator
 * @subpackage processors
 */
class StaticElementsMediaSourceGetListProcessor extends modObjectGetListProcessor {

  public $classKey = 'modMediaSource';
  public $objectType = 'sources.modMediaSource';
  public $permission = 'source_view';
  public $seaccelerator = null;


  /**
   * @return bool
   */
  public function initialize() {

    $initialized = parent::initialize();
    $this->modx->loadClass("seaccelerator.seaccelerator");
    $this->seaccelerator = new Seaccelerator($this->modx);

    return $initialized;
  }


  /**
   * @param xPDOObject $object
   * @return xPDOObject
   */
  public function prepareRow($object) {

    $objectArray = $object->toArray();

    return $objectArray;
  }
}

return 'StaticElementsMediaSourceGetListProcessor';
