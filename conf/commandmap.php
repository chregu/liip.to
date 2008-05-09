<?php
$m = new api_routing();

$m->route('/api/txt/*url')->config(array( 
    'command'=>'liipto',
    'method' => 'create',
    'view' => array('class' => 'txt')

));

$m->route('/api/qr/*url')->config(array( 
    'command'=>'liipto',
    'method' => 'create',
    'view' => array('class' => 'qr')

));


$m->route('/api/chk/*url')->config(array( 
    'command'=>'liipto',
    'method' => 'checkCode',
    'view' => array('class' => 'txt')

));

$m->route('/:url')
  ->config(array(
    'command' => 'liipto',
  'method' => 'redirect',
  'view' => array('class' => 'redirect')));
    
  
$m->route('/')
   ->config(array(
    'command' => 'default',
    'view' => array('xsl' => 'start.xsl')));