<?php

$version = (int) $argv[1];
chdir(dirname(__FILE__));

$maxOldVersion = 0;
foreach (glob('../live.*') as $dir) {
	$v = (int) preg_replace('#[^0-9]+#', '', $dir);
	if ($v > $maxOldVersion) {
		$maxOldVersion = $v;
	}
}

if (!$version && $maxOldVersion > 0) {
	
	$version = $maxOldVersion + 1;
	
}

foreach (glob('./www/static/yui/*_*_*') as $dir) {
var_dump(preg_match('#([0-9]+\_[0-9]+\_[0-9]+)#',$dir ,$matches));
$yuiversion = $matches[1];
}


if (! $version) {
	die("no version supplied");
}
$rootPath = dirname(dirname(__FILE__));
$oriPath = dirname(__FILE__);
$livePath = $rootPath . '/live';
$liveVersionPath = $livePath . "." . $version;

mkdir($liveVersionPath);

//copy config 
//FIXME... stage and live do not have the same config...
full_copy($oriPath . "/conf", $liveVersionPath . "/conf");

//inc 
mkdir($liveVersionPath . '/inc', 0777);
if (file_exists($oriPath . "/inc/lib")) {
	full_copy($oriPath . "/inc/lib", $liveVersionPath . "/inc");
}
full_copy($oriPath . "/inc", $liveVersionPath . "/inc", array($oriPath . "/inc/lib"));

//ext
foreach (glob($oriPath . '/ext/*/*') as $dir) {
	if (basename($dir) == 'lib') {
		full_copy($dir, $liveVersionPath . '/inc/');
	} else {
		full_copy($dir, $liveVersionPath . '/inc/' . basename($dir), array($dir . '/lib'));
	}
}

//localinc 
mkdir($liveVersionPath . '/localinc', 0777);
if (file_exists($oriPath . "/localinc/lib")) {
	full_copy($oriPath . "/localinc/lib", $liveVersionPath . "/localinc");
}
full_copy($oriPath . "/localinc", $liveVersionPath . "/localinc");

//themes
full_copy($oriPath . "/themes", $liveVersionPath . "/themes");

//lang
full_copy($oriPath . "/lang", $liveVersionPath . "/lang");

//tmp
mkdir($liveVersionPath . '/tmp', 0777);

file_put_contents($liveVersionPath . "/conf/config.d/02-live.yml", "live: 
    <<: *live-fixed
    webpaths:
        static: static/$version/
        yui: static/yui/$yuiversion/
");
//www
mkdir($liveVersionPath . '/www', 0755);
copy($oriPath . "/www/index.php", $liveVersionPath . "/www/index.php");
copy($oriPath . "/www/.htaccess", $liveVersionPath . "/www/.htaccess");
copy($oriPath . "/www/favicon.ico", $liveVersionPath . "/www/favicon.ico");

file_put_contents($liveVersionPath . "/www/.htaccess", file_get_contents($oriPath . '/www/.htaccess-live'), FILE_APPEND);

mkdir($liveVersionPath . '/www/static/', 0755);
//ohne yui
full_copy($oriPath . "/www/static", $liveVersionPath . "/www/static/" . $version, array($oriPath . '/www/static/yui'));
//yui only (ned versioniert)
mkdir($liveVersionPath . '/www/static/yui', 0755);

full_copy($oriPath . "/www/static/yui/", $liveVersionPath . "/www/static/yui/");

full_copy($oriPath . "/www/inc", $liveVersionPath . "/www/inc");

//link to old available version
chdir($rootPath);
chdir($liveVersionPath . '/www/static/');

foreach (glob('../../../live.*') as $dir) {
	$nr = substr(basename($dir), 5);

	if (substr($dir, 0, 6) == "live.$version") {
		continue;
	}
	
	
        if ($nr < $version - 10) {
		$delete =  $rootPath . '/live.' . ($nr);

		print "rm -rf   $delete\n";
		`rm -rf $delete`;


	} else {
		$target = '../../../live.' . $nr . '/www/static/' . $nr . '/';
        	
		if ($nr != $version && ! (is_link($target))) {
			@unlink($nr);
			symlink($target, $nr);
		
		}
	}
	//full_copy($dir,$liveVersionPath . '/inc/'.basename($dir));
}
chdir($rootPath);


@unlink('live');
symlink(basename($liveVersionPath), 'live');

function full_copy($source, $target, $exclude = array()) {
	print"Copying " . $source . "\n";
	if (is_dir($source)) {
		@mkdir($target);
		
		$d = dir($source);
		
		while (FALSE !== ($entry = $d->read())) {
			if ($entry == '.' || $entry == '..' || $entry == '.svn') {
				continue;
			}
			$Entry = $source . '/' . $entry;
			
			if (in_array($Entry, $exclude)) {
				print"FFFFF ";
				var_dump($exclude);
				
				continue;
			}
			if (is_dir($Entry)) {
				full_copy($Entry, $target . '/' . $entry, $exclude);
				continue;
			}
			copy($Entry, $target . '/' . $entry);
		}
		
		$d->close();
	} else {
		copy($source, $target);
	}

}


