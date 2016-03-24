<?php
/** @var $this OC\Route\Router */
$this->create('files_nbviewer_load', '/ajax/loadfile.php')
	->actionInclude('files_nbviewer/ajax/loadfile.php');

$this->create('files_nbviewer_can', '/ajax/canbeopen.php')
        ->actionInclude('files_nbviewer/ajax/canbeopen.php');

/** @var $this OC\Route\Router */
$this->create('files_nbviewer_canpublic', '/ajax/canbeopenpublic.php')
        ->actionInclude('files_nbviewer/ajax/canbeopenpublic.php');
        
$this->create('files_nbviewer_loadpublic', '/ajax/loadpublicfile.php')
		->actionInclude('files_nbviewer/ajax/loadpublicfile.php');