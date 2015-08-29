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
		$sources = $this->modx->getObject("sources.modMediaSource");
		foreach ($sources as $mediaSource) {
			$mediaSources .= "<option value='".$mediaSource->get('id')."'>".$mediaSource->get('name')."</option>\n";
		}

		break;
	case xPDOTransport::ACTION_UNINSTALL: break;
}

$output = '<label for="mediasource">Use the following Mediasource:</label>
<select name="mediasource" id="mediasource">';
$output .= $mediaSources;
$output .='</select>';

return $output;
