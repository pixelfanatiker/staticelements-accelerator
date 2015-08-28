<?php

$list = array();
$list[] = array('id' => 'templates', 'type' => 'Templates');
$list[] = array('id' => 'chunks', 'type' => 'Chunks');
$list[] = array('id' => 'snippets', 'type' => 'Snippets');
$list[] = array('id' => 'plugins', 'type' => 'Plugins');
$count = count($list);

return $this->outputArray($list, $count);
