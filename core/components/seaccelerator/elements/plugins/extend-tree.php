<?php
/**
 * StaticElements Accelerator
 * Plugin ExtendTree
 *
 * By Florian Gutwald <florian@frontend-mercenary.com>
 *
 * Published under the GNU Licence
 *
 * @package seaccelerator
 * @subpackage plugins
 */
if (!isset($modx->seaccelerator) || !is_object($modx->seaccelerator)) {
	$seaccelerator = $modx->getService('seaccelerator','Seaccelerator',$modx->getOption('seaccelerator.core_path',null,$modx->getOption('core_path').'components/seaccelerator/').'model/seaccelerator/', $scriptProperties);
	if (!($seaccelerator instanceof Seaccelerator)) return '---';
}

switch ($modx->event->name) {
	case 'OnManagerPageBeforeRender':

		$modx->log(xPDO::LOG_LEVEL_ERROR, "Seaccelerator @ OnManagerPageBeforeRender");

		if(empty($properties)){
			$properties = array();
		}
		/*$modx->regClientStartupHTMLBlock('<script type="text/javascript">
        Ext.onReady(function() {
            Seaccelerator.config = '.$modx->toJSON ($seaccelerator->config).';
        });
        </script>');*/

		$version = $modx->getVersionData();
		if($version['version'] == 2 && $version['major_version'] == 2){
			$modx->regClientCSS($seaccelerator->config['cssUrl'].'seaccelerator.css');
		}
		$modx->regClientStartupScript($seaccelerator->config['jsUrl'].'mgr/seaccelerator.js');
		$modx->regClientStartupScript($seaccelerator->config['jsUrl'].'mgr/widgets/extend-tree.js');

		$modx->controller->addLexiconTopic('seaccelerator:default');
		break;
}
return;
