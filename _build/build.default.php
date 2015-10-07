<?php ob_start();


define('PKG_CATEGORY',   '');  // Category that will be extracted
define('PKG_NAME_FULL',  'StaticElements Accelerator');
define('PKG_NAME_LOWER', 'seaccelerator');

define('PKG_VERSION',    '0.0.2');
define('PKG_RELEASE',    'alpha');



define('MODX_CORE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/core/');
define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');



/**
 * Advanced Settings / Configs
 * -------------------------------------------------------------------------------------------------------------------
 */

/* Set package options - you can turn these on one-by-one
 * as you create the transport package
 * */
$hasResources    = false;
$hasValidator    = false; /* Run a validator before installing anything */
$hasResolver     = false; /* Run a resolver after installing everything */
$hasSetupOptions = true; /* HTML/PHP script to interact with user */
$hasMenu         = true; /* Add items to the MODx Top Menu */
$hasSettings     = true; /* Add new MODx System Settings */

/* Note: property sets are connected to elements in the script
 * resolver (see _build/data/resolvers/install.script.php)
 */
$hasSubPackages = false; /* add in other component packages (transport.zip files)*/
/* Note: The package files will be copied to core/packages but will
 * have to be installed manually with "Add New Package" and "Search
 * Locally for Packages" in Package Manager. Be aware that the
 * copied packages may be older versions than ones already
 * installed. This is necessary because Package Manager's
 * autoinstall of the packages is unreliable at this point.
 */


/* set start time */
$mtime  = microtime();
$mtime  = explode(" ", $mtime);
$mtime  = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define sources */
$root    = dirname(dirname(__FILE__)) . '/';
$sources = array(
	'root'            => $root,
	'build'           => $root . '_build/',
	/* note that the next two must not have a trailing slash */
	'source_core'     => $root . 'core/components/' . PKG_NAME_LOWER,
	'source_assets'   => $root . 'assets/components/' . PKG_NAME_LOWER,
	'resolvers'       => $root . '_build/resolvers/',
	'validators'      => $root . '_build/validators/',
	'data'            => $root . '_build/data/',
	'docs'            => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
	'install_options' => $root . '_build/install.options/',
	'packages'        => $root . 'core/packages',
);
unset($root);

/*
 * Check Base Files
 */
$genericFiles = array(
	'license'       => @file_get_contents($sources['docs'] . 'license.txt'),
	'readme'        => @file_get_contents($sources['docs'] . 'readme.txt'),
	'changelog'     => @file_get_contents($sources['docs'] . 'changelog.txt'),
	# 'setup-options' => array( #'source' => $sources['install_options'].'user.input.php',
);
if($_GET['license'] == 'GPL') {setcookie('loadGPL',true, time() + 120); $_COOKIE['loadGPL'] = true;}
if($_GET['ignore'] == 'license') {setcookie('ignoreLicenseMissing',true, time() + 120); $_COOKIE['ignoreLicenseMissing'] = true;}

if($_COOKIE['loadGPL'] )
	$genericFiles['license'] = file_get_contents('http://www.gnu.org/licenses/gpl-3.0.txt');

if(!$genericFiles['license']) {
	echo "license missing<br>";
	echo "You could take for example the GPL from <a href='http://www.gnu.org/licenses/gpl-3.0.txt' target='_blank'>http://www.gnu.org/licenses/gpl-3.0.txt</a><br>";
	echo "create the file  <b>" . $sources['docs'] . 'license.txt</b>';
	echo "<br><a href='?ignore=readme'>ignore & continue</a> or <a href='?license=GPL'>use GPL</a>";
	die();
}

if($_POST['readme']) file_force_contents($sources['docs'] . 'readme.txt', $_POST['readme']);
if($_GET['ignore'] == 'readme') {setcookie('ignoreReadmeMissing',true, time() + 120); $_COOKIE['ignoreReadmeMissing'] = true;}
if(!$genericFiles['readme'] && $_COOKIE['ignoreReadmeMissing'] != true) {
	echo "readme missing<br>";
	echo "create the file  <b>" . $sources['docs'] . 'readme.txt</b>';
	echo "<br><a href='?ignore=readme'>ignore & continue</a>";
	die();
}
if($_POST['changelog']) file_force_contents($sources['docs'] . 'changelog.txt', $_POST['changelog']);
if($_GET['ignore'] == 'changelog') {setcookie('ignoreChangelogMissing',true, time() + 120); $_COOKIE['ignoreChangelogMissing'] = true;}
if(!$genericFiles['changelog'] && $_COOKIE['ignoreChangelogMissing'] != true) {
	echo "changelog missing<br>";
	echo "create the file  <b>" . $sources['docs'] . 'changelog.txt</b>';
	echo "<br><a href='?ignore=changelog'>ignore & continue</a>";
	die();
}



require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

/* load builder */
$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');


/* create category  The category is required and will automatically
 * have the name of your package
 */


$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_CATEGORY);

if (file_exists(MODX_CORE_PATH . 'components/' . PKG_NAME_LOWER)) $hasCore = true;
if (file_exists($root . 'assets/components/' . PKG_NAME_LOWER)) $hasAssets = true;


$categoryFromModx = $modx->getObject('modCategory', array('category' => PKG_CATEGORY));

addCompositesToObject($category, $categoryFromModx);


#$packFiles = array("Chunks", "Snippets", "Plugins", "Templates", "TemplateVars");
#foreach ($packFiles as $childName) {
#    /** @var XPDOSimpleObject[] $children */
#    $children             = $categoryFromModx->getMany($childName);
#
#    if ($children) {
#        $modx->log(modX::LOG_LEVEL_INFO, 'Adding in snippets.');
#        /* note: Snippets' default properties are set in transport.snippets.php */
#
#        if (is_array($children)) {
#            $modx->log(modX::LOG_LEVEL_INFO, 'Added snippets....');
#            $i      = 1;
#
#            /** @var xPDOObject[] $objects */
#            $objects = array();
#            foreach ($children as $childObj) {
#                $childObj->set('snippet', $childObj->get('content'));
#                $objects[$i] = $modx->newObject($childObj->_class);
#                $objects[$i]->fromArray($childObj->toArray(),'',true);
#                $objects[$i]->set('id',$i);
#
#                addCompositesToObject($objects[$i], $childObj);
#
#                $i++;
#            }
#            $category->addMany($objects, $childName);
#        } else {
#            $modx->log(modX::LOG_LEVEL_FATAL, 'Adding snippets failed.');
#        }
#    }
#}


/* Create Category attributes array dynamically
 * based on which elements are present
 */

$attr = array(xPDOTransport::UNIQUE_KEY      => 'category',
	xPDOTransport::PRESERVE_KEYS   => false,
	xPDOTransport::UPDATE_OBJECT   => true,
	xPDOTransport::RELATED_OBJECTS => true,
);

if ($hasValidator) {
	$attr[xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL] = true;
}
if (!!$category->getMany('Snippets')) {
	$attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippets'] = array(
		xPDOTransport::PRESERVE_KEYS => false,
		xPDOTransport::UPDATE_OBJECT => true,
		xPDOTransport::UNIQUE_KEY    => 'name',
	);
}

if (!!$category->getMany('PropertySets')) {
	$attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['PropertySets'] = array(
		xPDOTransport::PRESERVE_KEYS => false,
		xPDOTransport::UPDATE_OBJECT => true,
		xPDOTransport::UNIQUE_KEY    => 'name',
	);
}

if (!!$category->getMany('Chunks')) {
	$attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Chunks'] = array(
		xPDOTransport::PRESERVE_KEYS => false,
		xPDOTransport::UPDATE_OBJECT => true,
		xPDOTransport::UNIQUE_KEY    => 'name',
	);
}

if (!!$category->getMany('Plugins')) {
	$attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Plugins'] = array(
		xPDOTransport::PRESERVE_KEYS => false,
		xPDOTransport::UPDATE_OBJECT => true,
		xPDOTransport::UNIQUE_KEY    => 'name',
	);
}

if (!!$category->getMany('Templates')) {
	$attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Templates'] = array(
		xPDOTransport::PRESERVE_KEYS => false,
		xPDOTransport::UPDATE_OBJECT => true,
		xPDOTransport::UNIQUE_KEY    => 'templatename',
	);
}

if (!!$category->getMany('TemplateVars')) {
	$attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['TemplateVars'] = array(
		xPDOTransport::PRESERVE_KEYS => false,
		xPDOTransport::UPDATE_OBJECT => true,
		xPDOTransport::UNIQUE_KEY    => 'name',
	);
}

/* create a vehicle for the category and all the things
 * we've added to it.
 */

$vehicle = $builder->createVehicle($category, $attr);

if ($hasValidator) {
	$modx->log(modX::LOG_LEVEL_INFO, 'Adding in Script Validator.');
	$vehicle->validate('php', array(
		'source' => $sources['validators'] . 'preinstall.script.php',
	));
}

/* package in script resolver if any */
if ($hasResolver) {
	$modx->log(modX::LOG_LEVEL_INFO, 'Adding in Script Resolver.');
	$vehicle->resolve('php', array(
		'source' => $sources['resolvers'] . 'install.script.php',
	));
}
/* This section transfers every file in the local
 mycomponents/mycomponent/assets directory to the
 target site's assets/mycomponent directory on install.
 If the assets dir. has been renamed or moved, they will still
 go to the right place.
 */

if ($hasCore) {
	$vehicle->resolve('file', array(
		'source' => $sources['source_core'],
		'target' => "return MODX_CORE_PATH . 'components/';",
	));
}

/* This section transfers every file in the local
 mycomponents/mycomponent/core directory to the
 target site's core/mycomponent directory on install.
 If the core has been renamed or moved, they will still
 go to the right place.
 */

if ($hasAssets) {
	$vehicle->resolve('file', array(
		'source' => $sources['source_assets'],
		'target' => "return MODX_ASSETS_PATH . 'components/';",
	));
}

/* Add subpackages */
/* The transport.zip files will be copied to core/packages
 * but will have to be installed manually with "Add New Package and
 *  "Search Locally for Packages" in Package Manager
 */

if ($hasSubPackages) {
	$modx->log(modX::LOG_LEVEL_INFO, 'Adding in subpackages.');
	$vehicle->resolve('file', array(
		'source' => $sources['packages'],
		'target' => "return MODX_CORE_PATH;",
	));
}

/* Put the category vehicle (with all the stuff we added to the
 * category) into the package
 */
$builder->putVehicle($vehicle);


/* Transport Resources */

if ($hasResources) {
	$resources = include $sources['data'] . 'transport.resources.php';
	if (!is_array($resources)) {
		$modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in resources.');
	} else {
		$attributes = array(
			xPDOTransport::PRESERVE_KEYS             => false,
			xPDOTransport::UPDATE_OBJECT             => true,
			xPDOTransport::UNIQUE_KEY                => 'pagetitle',
			xPDOTransport::RELATED_OBJECTS           => true,
			xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
				'ContentType' => array(
					xPDOTransport::PRESERVE_KEYS => false,
					xPDOTransport::UPDATE_OBJECT => true,
					xPDOTransport::UNIQUE_KEY    => 'name',
				),
			),
		);
		foreach ($resources as $resource) {
			$vehicle = $builder->createVehicle($resource, $attributes);
			$builder->putVehicle($vehicle);
		}
		$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($resources) . ' resources.');
	}
	unset($resources, $resource, $attributes);
}

/* Transport Menus */
if ($hasMenu) {
	/* load menu */
	$modx->log(modX::LOG_LEVEL_INFO, 'Packaging in menu...');
	$menu = include $sources['data'] . 'transport.menu.php';
	if (empty($menu)) {
		$modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in menu.');
	} else {
		$vehicle = $builder->createVehicle($menu, array(
			xPDOTransport::PRESERVE_KEYS             => true,
			xPDOTransport::UPDATE_OBJECT             => true,
			xPDOTransport::UNIQUE_KEY                => 'text',
			xPDOTransport::RELATED_OBJECTS           => true,
			xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
				'Action' => array(
					xPDOTransport::PRESERVE_KEYS => false,
					xPDOTransport::UPDATE_OBJECT => true,
					xPDOTransport::UNIQUE_KEY    => array('namespace', 'controller'),
				),
			),
		));
		$builder->putVehicle($vehicle);

		$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($menu) . ' menu items.');
		unset($vehicle, $menu);
	}
}

/* load system settings */
if ($hasSettings) {
	$settings = include $sources['data'] . 'transport.settings.php';
	if (!is_array($settings)) {
		$modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in settings.');
	} else {
		$attributes = array(
			xPDOTransport::UNIQUE_KEY    => 'key',
			xPDOTransport::PRESERVE_KEYS => true,
			xPDOTransport::UPDATE_OBJECT => false,
		);
		foreach ($settings as $setting) {
			$vehicle = $builder->createVehicle($setting, $attributes);
			$builder->putVehicle($vehicle);
		}
		$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' System Settings.');
		unset($settings, $setting, $attributes);
	}
}

/**
 * Next-to-last step - pack in the license file, readme.txt, changelog,
 * and setup options
 */

$builder->setPackageAttributes($genericFiles);
#die();
/* Last step - zip up the package */
$builder->pack();

/* report how long it took */
$mtime     = microtime();
$mtime     = explode(" ", $mtime);
$mtime     = $mtime[1] + $mtime[0];
$tend      = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(xPDO::LOG_LEVEL_INFO, "Package [".PKG_NAME." ".PKG_VERSION.'-'.PKG_RELEASE."] Built.");
$modx->log(xPDO::LOG_LEVEL_INFO, "Execution time: {$totalTime}");



if (file_exists($builder->directory . $builder->filename)) {
	ob_end_clean();

	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"" . $builder->filename . "\"");
	header("Content-Length: " . filesize($builder->directory . $builder->filename));

	readfile($builder->directory . $builder->filename);
	die();
}

/**
 * @param $dir
 * @param $contents
 */
function file_force_contents($dir, $contents){
	$parts = explode('/', $dir);
	$file = array_pop($parts);
	$dir = '';
	foreach($parts as $part)
		if(!is_dir($dir .= "/$part")) mkdir($dir);
	file_put_contents("$dir/$file", $contents);
}

/**
 * @param $writeObject
 * @param xPDOObject $readObject
 */
function addCompositesToObject(&$writeObject, xPDOObject $readObject) {
	global $modx;

	// check composites
	$linkAggregate = $readObject->_composites;
	if($readObject->_class == 'modCategory') {
		$linkAggregate += (array) $readObject->_aggregates;
		unset($linkAggregate['Parent']);
		unset($linkAggregate['Ancestors']);
		unset($linkAggregate['Descendants']);
		unset($linkAggregate['modChunk']);
		unset($linkAggregate['modSnippet']);
		unset($linkAggregate['modPlugin']);
		unset($linkAggregate['modTemplate']);
		unset($linkAggregate['modTemplateVar']);
		unset($linkAggregate['modPropertySet']);
	}

	foreach($linkAggregate as $alias => $compData){
		// Do not include ACL`s to package
		if($alias == 'Acls') continue;


		var_dump($compData['class']);
		$i = 1;
		if($compData['cardinality'] == 'many') {
			/** @var xPDOObject[] $composites */
			$composites = array();
			var_dump($alias);
			var_dump(count($readObject->getMany($alias)));
			echo "<br>";
			foreach($readObject->getMany($alias) as $compositeObj) {

				$composites[$i] = $modx->newObject($compData['class']);
				$composites[$i]->fromArray($compositeObj->toArray(),'',true);

				if(count(array_diff_key($composites[$i]->_composites, array("Acls")))) {
					addCompositesToObject($composites[$i], $compositeObj);
				}
				$i++;
			}
			$writeObject->addMany($composites);
		} elseif($compositeObj = $writeObject->getOne($alias)) {
			$childObject = $modx->newObject($compData['class'], $compositeObj->toArray());
			addCompositesToObject($childObject, $compositeObj);
			$writeObject->addOne($childObject);
		}
	}
}
