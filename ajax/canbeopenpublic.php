<?php

// Set the session key for the file we are about to edit.
$token = isset($_GET['token']) ? $_GET['token'] : '';
if(!empty($token)) {

	$linkItem = \OC::$server->getShareManager()->getShareByToken($token);
	$owner = $linkItem->getShareOwner();

	\OC\Files\Filesystem::init($owner, '/' . $owner . '/files');

	$path = '/' . \OC\Files\Filesystem::getPath($linkItem->getNodeId());

	$maxsize = \OCP\Config::getSystemValue("max_size_notebook_file", 4194304); // default of 4MB
	$size = \OC\Files\Filesystem::filesize($path);
	if($size > $maxsize) {
		OCP\JSON::error(array('data' => array( 'message' => "The maximun file size for opening notebook files is $maxsize bytes.")));
		return;
	}
	OCP\JSON::success(array('data' => array('message' => "The file is allowed to be open in the Notebook Viewer", 'filename' => basename($path), 'dir' => dirname($path))));

} else {
	OCP\JSON::error(array('data' => array( 'message' => 'Invalid share token supplied.')));
}

?>