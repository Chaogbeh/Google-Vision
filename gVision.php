<?php
ini_set('display_errors', 1);
require_once SITE_ROOT . '/vendor/autoload.php';
require_once BUSINESS_DIR . 'GoogleVisionManager.php';
require_once BUSINESS_DIR . 'GoogleStorageManager.php';
Sentry\init(['dsn' => SENTRY_DSN ]);

// Some urls in the src attribute of the img tag are not formatted properly
// this is a fix
function _mFix_url($url) {
	if (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://') {
		return $url;
	}
	elseif (substr($url, 0, 2) == '//') {
		return 'https:'.$url;
	}
}

// Get the contents of the request body
//
$inputJSONString = file_get_contents('php://input');
$url = json_decode($inputJSONString, TRUE);

// Check if a base64 image is sent
//
if (isset($url['base64GsUrl'])) {
	$img = str_replace('data:image/png;base64,', '', $url['base64GsUrl']);
	$img = str_replace(' ', '+', $img);

	$image_name = GoogleStorageManager::uploadBase64Blob('uploads', $img)->webUrl();

	$labels = GoogleVisionManager::getInstance()->getImageLabels($image_name);

	header('Content-Type: application/json');

	if ($labels) {
		echo json_encode(array('labels' => $labels, 'image_url' => $image_name));
		exit();
	}
	else {
		echo json_encode(array('error' => 'Something went wrong'));
		exit();
	}
}

// Gets the url to the image location required for extraction
//
$targetURL = $url['gsUrl'];

// This is what does the actual label detection
//
$labels = GoogleVisionManager::getInstance()->getImageLabels(_mFix_url($targetURL));

header('Content-Type: application/json');

if ($labels) {
	echo json_encode(array('labels' => $labels));
	exit();
}
else {
	echo json_encode(array('error' => 'Something went wrong'));
	exit();
}
?>
