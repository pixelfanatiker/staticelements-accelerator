<?php
/**
* Adds modActions and modMenus into package
*
* @package seaccelerator
* @subpackage build
*/

$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'seaccelerator',
    'parent' => 0,
    'controller' => 'home',
    'haslayout' => true,
    'lang_topics' => 'seaccelerator:default',
    'assets' => '',
),'',true,true);

/* load action into menu */
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'seaccelerator.title',
    'parent' => 'components',
    'description' => 'seaccelerator.description',
    'icon' => '',
    'menuindex' => 0,
    'params' => '',
    'handler' => '',
),'',true,true);
$menu->addOne($action);
unset($menus);

return $menu;
