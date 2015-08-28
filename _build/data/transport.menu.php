<?php
/**
* Adds modActions and modMenus into package
*
* @package mycomponent
* @subpackage build
*/
$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'seaccelerator',
    'parent' => 0,
    'controller' => 'home',
    'haslayout' => true,
    'lang_topics' => 'seaccelerator.:default,lexicon',
    'assets' => '',
),'',true,true);

/* load action into menu */
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'seaccelerator.title',
    'parent' => 'seaccelerator',
    'description' => 'seaccelerator.description',
    'icon' => 'images/icons/plugin.gif',
    'menuindex' => 2,
    'params' => '',
    'handler' => '',
),'',true,true);
$menu->addOne($action);

return $menu;
