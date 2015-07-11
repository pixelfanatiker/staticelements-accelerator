<?php
class MultidomainacceleratorIndexManagerController extends modExtraManagerController {

	/** @var MultiDomainAccelerator $multiDomainAccelerator */
	public $multiDomainAccelerator;

	public function initialize () {
		$this->multiDomainAccelerator = new MultiDomainAccelerator($this->modx);
		//$this->modx->log(xPDO::LOG_LEVEL_ERROR, "index.class.php initializer");

		$this->addCss ($this->multiDomainAccelerator->config['cssUrl'] . 'multidomainaccelerator.css');
		$this->addJavascript ($this->multiDomainAccelerator->config['jsUrl'] . 'mgr/multidomainaccelerator.js');

		$this->addHtml ('<script type="text/javascript">
        Ext.onReady(function() {
            MultiDomainAccelerator.config = '.$this->modx->toJSON ($this->multiDomainAccelerator->config).';
        });
        </script>');

		return parent::initialize ();
	}

	public function checkPermissions() {
		return true;
	}

	public function process (array $scriptProperties = array ()) {}

	public function getPageTitle () {
		return $this->modx->lexicon ('multidomainaccelerator.title');
	}

	public function getLanguageTopics () {
		return array ('multidomainaccelerator:default');
	}

	public function loadCustomCssJs () {
		$this->addJavascript ($this->multiDomainAccelerator->config['jsUrl'].'mgr/widgets/tenants.grid.js');
		$this->addJavascript ($this->multiDomainAccelerator->config['jsUrl'].'mgr/widgets/home.panel.js');
		$this->addJavascript ($this->multiDomainAccelerator->config['jsUrl'].'mgr/sections/index.js');
	}

	public function getTemplateFile () {
		return $this->multiDomainAccelerator->config['templatesPath'] . 'home.tpl';
	}


}
