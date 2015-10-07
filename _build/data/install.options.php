<?php
/**
 * Build the setup options form.
 *
 * @package quip
 * @subpackage build
 */
/* set some default values */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
  case xPDOTransport::ACTION_INSTALL:
  case xPDOTransport::ACTION_UPGRADE:
    $modMediaSources = $this->modx->getObject("modMediaSource", 1);

    $modx->log(modX::LOG_LEVEL_INFO,'Loading media sources...');

    if (is_object($modMediaSources)) {
      foreach ($modMediaSources as $mediaSource) {
        $modx->log(modX::LOG_LEVEL_INFO,'Get media source ' . $mediaSource->get('name'));
        $mediaSources = "<option value='".$mediaSource->get('id')."'>".$mediaSource->get('name')."</option>\n";
      }
    } else {

    }

    break;

  case xPDOTransport::ACTION_UNINSTALL:
    break;
}

$output = '<label for="mediasource">Use the following mediasource for your static files:</label>
<select name="mediasource" id="mediasource">';
$output .= $mediaSources;
$output .='</select>';

return $output;
