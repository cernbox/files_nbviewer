<?php
// Check if we are a user
OCP\JSON::checkLoggedIn();

// Set the session key for the file we are about to edit.
$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
$filename = isset($_GET['file']) ? $_GET['file'] : '';
if(!empty($filename))
{
	$path = $dir.'/'.$filename;
	$maxsize = \OCP\Config::getSystemValue("max_size_notebook_file", 4194304); // default of 4MB
	$size = \OC\Files\Filesystem::filesize($path);
	if($size > $maxsize) {
		OCP\JSON::error(array('data' => array( 'message' => "The maximun file size for opening notebook files is $maxsize bytes.")));
		return;
	}
	OCP\JSON::success(array('data' => array('message' => "The file is allowed to be open in the Notebook Viewer")));

} else {
	OCP\JSON::error(array('data' => array( 'message' => 'Invalid file path supplied.')));
}
