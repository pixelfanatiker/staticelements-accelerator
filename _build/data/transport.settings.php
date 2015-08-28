<?php
/** Array of system settings for Mycomponent package
 * @package mycomponent
 * @subpackage build
 */


/* This section is ONLY for new System Settings to be added to
 * The System Settings grid. If you include existing settings,
 * they will be removed on uninstall. Existing setting can be
 * set in a script resolver (see install.script.php).
 */
$settings = array();

/* The first three are new settings */
$settings['seaccelerator.elements_directory']= $modx->newObject('modSystemSetting');
$settings['seaccelerator.elements_directory']->fromArray(array (
	'key' => 'seaccelerator.elements_directory',
	'value' => '',
	'namespace' => 'seaccelerator',
	'area' => 'Settings',
), '', true, true);

$settings['seaccelerator.mediasource']= $modx->newObject('modSystemSetting');
$settings['seaccelerator.mediasource']->fromArray(array (
	'key' => 'seaccelerator.mediasource',
	'xtype' => 'modx-combo-source',
	'value' => '1',
	'namespace' => 'seaccelerator',
	'area' => 'Settings',
), '', true, true);
$settings['seaccelerator.use_categories']= $modx->newObject('modSystemSetting');
$settings['seaccelerator.use_categories']->fromArray(array (
	'key' => 'seaccelerator.use_categories',
	'xtype' => 'combo-boolean',
	'value' => '1',
	'namespace' => 'seaccelerator',
	'area' => 'Settings',
), '', true, true);


return $settings;
