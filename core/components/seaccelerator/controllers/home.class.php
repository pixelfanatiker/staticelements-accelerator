<?php

require_once dirname(dirname (__FILE__)) . '/model/seaccelerator/seaccelerator.class.php';
/**
 * Class SeacceleratorHomeManagerController
 */
class SeacceleratorHomeManagerController extends modExtraManagerController {

	/** @var Nomination $seaccelerator */
	public $seaccelerator;

	public function initialize () {

		$this->seaccelerator = new Seaccelerator($this->modx);
		//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "index.class.php initializer");

		$this->addCss ($this->seaccelerator->config['cssUrl'] . 'seaccelerator.css');
		$this->addJavascript ($this->seaccelerator->config['jsUrl'] . 'mgr/seaccelerator.js');

		$this->addHtml ('<script type="text/javascript">
        Ext.onReady(function() {
            Seaccelerator.config = '.$this->modx->toJSON ($this->seaccelerator->config).';
        });
        </script>');

		return parent::initialize ();
	}

	public function checkPermissions() {
		return true;
	}

	//public function process (array $scriptProperties = array ()) {}

	public function getPageTitle () {
		return $this->modx->lexicon ('seaccelerator.title');
	}

	public function getLanguageTopics () {
		return array ('seaccelerator:default');
	}

	public function loadCustomCssJs () {
		$this->addJavascript($this->seaccelerator->config['jsUrl'].'mgr/widgets/elements.grid.js');
		$this->addJavascript($this->seaccelerator->config['jsUrl'].'mgr/widgets/files.grid.js');
		$this->addJavascript($this->seaccelerator->config['jsUrl'].'mgr/widgets/home.panel.js');
		$this->addLastJavascript($this->seaccelerator->config['jsUrl'].'mgr/sections/home.js');
	}

	public function getTemplateFile () {
		//return $this->nomination->config['templatesPath'] . 'home.tpl';
		return "home.tpl";
	}
}

class HomeManagerController extends SeacceleratorHomeManagerController {
	public static function getDefaultController() { return 'home'; }
}
