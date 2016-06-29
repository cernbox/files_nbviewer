<?php

// Check if we are a user
OCP\JSON::checkLoggedIn();
OCP\JSON::callCheck();

\OC::$server->getSession()->close();

// Set the session key for the file we are about to edit.
$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
$filename = isset($_GET['file']) ? $_GET['file'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';
if(!empty($filename))
{
        header('Content-Type: text/plain');
        
        if(!empty($token))
        {
        	$linkItem = \OC::$server->getShareManager()->getShareByToken($token);
        	$owner = $linkItem->getShareOwner();
        	\OC\Files\Filesystem::init($owner, '/' . $owner . '/files');
        	$dir = '/' . \OC\Files\Filesystem::getPath($linkItem->getNodeId());
        	$dir = rtrim($dir, '/');
        }
        
        $path = $dir.'/'.$filename;
        $filecontents = \OC\Files\Filesystem::file_get_contents($path);

        // Needed variables
        $pythonLib = \OCP\Config::getSystemValue('pythonLib', '/opt/rh/python27/root/usr/lib64');
        $pythonBin = \OCP\Config::getSystemValue('pythonBin', '/opt/rh/python27/root/usr/bin/python');
        $pythonInputHack = \OCP\Config::getSystemValue('pythonInputHack', '/cernbox/apps/files_nbviewer/python/input_hack.py');
        //putenv('LD_LIBRARY_PATH=' . $pythonLib);

        // Convert notebook
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
           1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
           2 => array("file", dirname($pythonInputHack) . '/error_log.log', "a") // stderr is a file to write to
        );
        $pipes = [];
        $returnValue = 0;
        
        $process = proc_open("$pythonBin $pythonInputHack", $descriptorspec, $pipes, NULL, ['LD_LIBRARY_PATH'=>$pythonLib]);
        fwrite($pipes[0], $filecontents);
        fclose($pipes[0]);
        
        $result = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $returnValue = proc_close($process);
        if($returnValue === 0)
        {
        	OCP\JSON::success(['data' => ['content' => $result]]);
        }
        else
        {
        	\OCP\Util::writeLog('files_nbviewer', 'Error while converting notebook. Return code: ' .$returnValue, \OCP\Util::ERROR);
        	OCP\JSON::error(['data' => ['message' => 'A problem occoured while loading the Notebook']]);
        }
        return;
	
} else {
	OCP\JSON::error(['data' => ['message' => 'Invalid file path supplied.']]);
}
