<?php
/** @var $this OC\Route\Router */
$this->create('files_nbviewer_load', '/ajax/loadfile.php')
	->actionInclude('files_nbviewer/ajax/loadfile.php');

$this->create('files_nbviewer_can', '/ajax/canbeopen.php')
        ->actionInclude('files_nbviewer/ajax/canbeopen.php');