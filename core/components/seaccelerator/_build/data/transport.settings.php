<?php
/**
 * Loads system settings into build
 *
 * @package semanager
 * @subpackage build
 */

$settings = array();

$settings[0]= $modx->newObject('modSystemSetting');
$settings[0]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.elements_dir',
    'value' => '{assets_path}elements/',
    'xtype' => 'textfield',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[1]= $modx->newObject('modSystemSetting');
$settings[1]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.filename_tpl_chunk',
    'value' => 'html',
    'xtype' => 'textfield',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[2]= $modx->newObject('modSystemSetting');
$settings[2]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.filename_tpl_plugin',
    'value' => 'php',
    'xtype' => 'textfield',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[3]= $modx->newObject('modSystemSetting');
$settings[3]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.filename_tpl_snippet',
    'value' => 'php',
    'xtype' => 'textfield',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[4]= $modx->newObject('modSystemSetting');
$settings[4]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.filename_tpl_template',
    'value' => 'html',
    'xtype' => 'textfield',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[5]= $modx->newObject('modSystemSetting');
$settings[5]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.type_separation',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'semanager',
    'area' => 'Other',
),'',true,true);

$settings[6]= $modx->newObject('modSystemSetting');
$settings[6]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.use_categories',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'semanager',
    'area' => 'Other',
),'',true,true);

$settings[7]= $modx->newObject('modSystemSetting');
$settings[7]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.use_suffix_only',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[8]= $modx->newObject('modSystemSetting');
$settings[8]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.elements_mediasource',
    'value' => 1,
    'xtype' => 'modx-combo-source',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[9]= $modx->newObject('modSystemSetting');
$settings[9]->fromArray(array(
    'key' => PKG_NAME_LOWER.'.use_mediasources',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

return $settings;
