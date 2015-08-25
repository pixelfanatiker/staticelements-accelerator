<?php
/**
 * Loads system settings into build
 *
 * @package semanager
 * @subpackage build
 */

$action = $modx->newObject('modAction');
$action->fromArray(array(
    'id'=>'200',
    'namespace' => 'semanager',
    'parent' => 0,
    'controller' => 'home',
    'haslayout' => true,
    'lang_topics' => 'semanager:default',
    'assets' => '',
),'',true,true);
//die(var_dump($action->id));
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'semanager.title',
    'parent' => 'components',
    'description' => 'semanager.description',
    'action' => $action->id,
    'menuindex' => 0,
    'params' => '',
    'handler' => '',
),'',true,true);
$menu->addOne($action);
unset($menus);

return $menu;
