<?php
$m = new api_routing();
$x = new api_routing_regex();

$m->route('/api/txt/*url')->config(array(
    'command'=>'liipto',
    'method' => 'create',
    'view' => array('class' => 'txt')

));

$m->route('/api/txt140/*url')->config(array(
    'command'=>'liipto',
    'method' => 'create140',
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

$m->route('/api/rchk/*url')->config(array(
    'command'=>'liipto',
    'method' => 'checkCodeReverse',
    'view' => array('class' => 'txt')

));

$m->route('/api/rchkrev/*url')->config(array(
    'command'=>'liipto',
    'method' => 'checkCodeReverseAndRevCan',
    'view' => array('class' => 'txt')

));


$m->route('/api/resolve/*url')->config(array(
    'command'=>'liipto',
    'method' => 'resolve',
    'view' => array('class' => 'txt')

));

$m->route('/api/revcan/*url')->config(array(
    'command'=>'liipto',
    'method' => 'revcan',
    'view' => array('class' => 'txt')

));

$x->route('/(?<url>.+:/.*)')->config(array(
    'command' => 'liipto',
    'method' => 'createFromPath',
    'view' => array('class' => 'txt')));

$m->route('/:url')->config(array(
    'command' => 'liipto',
    'method' => 'redirect',
    'view' => array('class' => 'redirect')));


$m->route('/')->config(array(
    'command' => 'default',
    'view' => array('xsl' => 'start.xsl')));
