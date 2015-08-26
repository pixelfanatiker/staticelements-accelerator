<?php
/**
 * Seaccelerator Connector
 *
 * @package seaccelerator
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('seaccelerator.core_path',null,$modx->getOption('core_path').'components/seaccelerator/');
require_once $corePath.'model/seaccelerator/seaccelerator.class.php';
$modx->seaccelerator = new Seaccelerator($modx);

$modx->lexicon->load('seaccelerator:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->seaccelerator->config,$corePath.'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));
