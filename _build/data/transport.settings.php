<?php
/** Array of system settings for StaticElements Accelerator
 * @package seaccelerator
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
    'key' => PKG_NAME_LOWER . ' elements_directory',
    'value' => 'elements',
    'namespace' => 'seaccelerator',
    'name' => 'seaccelerator.elements_directory.name',
    'description' => 'seaccelerator.elements_directory.description',
    'area' => 'seaccelerator.namespace.settings',
), '', true, true);

$settings['seaccelerator.mediasource']= $modx->newObject('modSystemSetting');
$settings['seaccelerator.mediasource']->fromArray(array (
    'key' => PKG_NAME_LOWER . ' mediasource',
    'xtype' => 'modx-combo-source',
    'value' => '1',
    'namespace' => 'seaccelerator',
    'name' => 'seaccelerator.mediasource.name',
    'description' => 'seaccelerator.mediasource.description',
    'area' => 'seaccelerator.namespace.settings',
), '', true, true);

$settings['seaccelerator.use_categories']= $modx->newObject('modSystemSetting');
$settings['seaccelerator.use_categories']->fromArray(array (
    'key' => PKG_NAME_LOWER . ' use_categories',
    'xtype' => 'combo-boolean',
    'value' => '1',
    'namespace' => 'seaccelerator',
    'name' => 'seaccelerator.use_categories.name',
    'description' => 'seaccelerator.use_categories.description',
    'area' => 'seaccelerator.namespace.settings',
), '', true, true);

$settings['seaccelerator.element_type_separation']= $modx->newObject('modSystemSetting');
$settings['seaccelerator.element_type_separation']->fromArray(array (
  'key' => PKG_NAME_LOWER . ' element_type_separation',
  'xtype' => 'text',
  'value' => 'folder',
  'namespace' => 'seaccelerator',
  'name' => 'seaccelerator.element_type_separation.name',
  'description' => 'seaccelerator.element_type_separation.description',
  'area' => 'seaccelerator.namespace.settings',
), '', true, true);

$settings['seaccelerator.element_type_rules']= $modx->newObject('modSystemSetting');
$settings['seaccelerator.element_type_rules']->fromArray(array (
  'key' => PKG_NAME_LOWER . ' element_type_rules',
  'xtype' => 'text',
  'value' => 'modChunk:chunks,modSnippet:snippets,modTemplate:templates,modPlugin:plugins',
  'namespace' => 'seaccelerator',
  'name' => 'seaccelerator.element_type_rules.name',
  'description' => 'seaccelerator.element_type_rules.description',
  'area' => 'seaccelerator.namespace.settings',
), '', true, true);


return $settings;
