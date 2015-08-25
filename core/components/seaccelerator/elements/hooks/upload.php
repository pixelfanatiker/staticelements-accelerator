<?php

$path = "assets/images/nominations/2016/"; // Path from root that user specifies
$extensions = ["jpg"];

$extensionsArray = explode(',', $extensions);

$basePath = $modx->config['base_path']; // Site root
$targetPath = $basePath . $path; // root /assets/upload


$filename = basename($_FILES['car_image']['name']);
$filetype = pathinfo($filename, PATHINFO_EXTENSION);

$modx->log(xPDO::LOG_LEVEL_ERROR, "filename: ".$filename);


if ($filename != '') {

	$driverName = mb_strtolower($hook->get("driver_name"));
	$carBrand = mb_strtolower($hook->get("car_brand"));
	$carType = mb_strtolower($hook->get("car_type"));
	$driverInfo = $driverName."_".$carBrand."_".$carType;

	$filename = mb_strtolower($driverInfo);
	$filename = str_replace(' ', '_', $filename);
	$filename = date("Ymdgi") . $filename;

	// Set final path
	$targetPath = $targetPath . $filename;

	if (in_array($filetype, $extensionsArray)) {
		if (move_uploaded_file($_FILES['car_image']['tmp_name'], $targetPath)) {
			// Upload successful
			//$hook->setValue('car_image',$_FILES['car_image']['name']);
			$hook->setValue('car_image', $filename);
			return true;
		} else {
			// File not uploaded
			$errorMsg = 'File not uploaded.';
			$hook->addError('car_image', $errorMsg);
			return false;
		}
	} else {
		// File type not allowed
		$errorMsg = 'File not allowed.';
		$hook->addError('car_image', $errorMsg);
		return false;
	}
} else {
	$hook->setValue('car_image', '');
	return true;
}

return true;
?>