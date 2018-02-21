<?php

// Borrowed from https://github.com/IMSGlobal/LTI-Sample-Tool-Provider-PHP

###
###  Get the web path to the application
###
  function getAppPath() {

    $root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    if (substr($root, -1) === '/') {  // remove any trailing / which should not be there
      $root = substr($root, 0, -1);
    }
    $dir = str_replace('\\', '/', dirname(__FILE__));

    $path = str_replace($root, '', $dir) . '/';

    return $path;

  }


###
###  Get the application domain URL
###
  function getHost() {

    $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on")
              ? 'http'
              : 'https';
    $url = $scheme . '://' . $_SERVER['HTTP_HOST'];

    return $url;

  }


###
###  Get the URL to the application
###
  function getAppUrl() {

    $url = getHost() . getAppPath();

    return $url;

  }

?>