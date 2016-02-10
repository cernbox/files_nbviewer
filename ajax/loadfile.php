<?php

// Check if we are a user
OCP\JSON::checkLoggedIn();
OCP\JSON::callCheck();

// Set the session key for the file we are about to edit.
$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
$filename = isset($_GET['file']) ? $_GET['file'] : '';
if(!empty($filename))
{
        header('Content-Type: text/plain');
        $path = $dir.'/'.$filename;
        $filecontents = \OC\Files\Filesystem::file_get_contents($path);

        // Needed variables
        $pythonLib = \OCP\Config::getSystemValue('pythonLib', '/opt/rh/python27/root/usr/lib64');
        $pythonBin = \OCP\Config::getSystemValue('pythonBin', '/opt/rh/python27/root/usr/bin/python');
        $pythonInputHack = \OCP\Config::getSystemValue('pythonInputHack', '/cernbox/apps/files_nbviewer/python/input_hack.py');
        // Set up python enviroment
        putenv('LD_LIBRARY_PATH=' . $pythonLib);

        // Convert notebook
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
           1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
           2 => array("file", dirname($pythonInputHack) . '/error_log.log', "a") // stderr is a file to write to
        );
        $pipes = [];
        $escapedContent = $filecontents;
        //$escapedContent = substr($escapedContent, 1, count($escapedContent) - 2);
        $returnValue = 0;
        
        $process = proc_open("$pythonBin $pythonInputHack", $descriptorspec, $pipes, NULL, ['LD_LIBRARY_PATH'=>$pythonLib]);
        fwrite($pipes[0], $escapedContent);
        //fflush($pipes[0]);
        fclose($pipes[0]);
        //\OCP\Util::writeLog('NB CONVERTER', 'PYTHON HACK RETURN CODE: ' . $returnValue, \OCP\Util::ERROR);
        $result = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $returnValue = proc_close($process);
        \OCP\Util::writeLog('NB CONVERTER', 'PYTHON HACK RETURN CODE: ' . $returnValue, \OCP\Util::ERROR);
        // Glue it and send to client
        OCP\JSON::success(['data' => ['content' => $result]]);
        //echo implode('', $result);
        return;
	
} else {
	OCP\JSON::error(array('data' => array( 'message' => 'Invalid file path supplied.')));
}
