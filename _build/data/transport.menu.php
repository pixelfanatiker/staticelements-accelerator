<?php
/**
* Adds modActions and modMenus into package
*
* @package mycomponent
* @subpackage build
*/
$action= $modx->newObject('modAction');

/* load action into menu */
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'seaccelerator.title',
    'parent' => 'components',
    'description' => 'seaccelerator.description',
    'params' => '',
    'handler' => '',
),'',true,true);
$menu->addOne($action);

return $menu;
