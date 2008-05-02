<?php

$version = $argv[1];

if (!$version) {
    die("no version supplied");
}
$rootPath = dirname(dirname(__FILE__));
$oriPath = dirname(__FILE__) ;
$livePath = $rootPath.'/live';
$liveVersionPath = $livePath .".".$version;

mkdir($liveVersionPath);

//copy config 
//FIXME... stage and live do not have the same config...
full_copy($oriPath ."/conf", $liveVersionPath ."/conf");

//inc 
full_copy($oriPath ."/inc", $liveVersionPath ."/inc");

//localinc 
full_copy($oriPath ."/localinc", $liveVersionPath ."/localinc");

//themes
full_copy($oriPath ."/themes", $liveVersionPath ."/themes");

//lang
full_copy($oriPath ."/lang", $liveVersionPath ."/lang");

//tmp
mkdir($liveVersionPath.'/tmp',0777);
//ext
foreach (glob($oriPath . '/ext/*/*') as $dir) {
    full_copy($dir,$liveVersionPath . '/inc/'.basename($dir));
}


file_put_contents($liveVersionPath."/conf/config.d/02-live.yml",
"live: 
    <<: *live-fixed
    webpaths:
        static: static/$version/
"
);
//www
mkdir($liveVersionPath.'/www',0755);
copy($oriPath."/www/index.php",$liveVersionPath."/www/index.php");
copy($oriPath."/www/.htaccess",$liveVersionPath."/www/.htaccess");

mkdir($liveVersionPath.'/www/static/',0755);
full_copy($oriPath ."/www/static", $liveVersionPath ."/www/static/".$version);

//link to old available version
chdir($rootPath);
chdir($liveVersionPath.'/www/static/');
        
foreach (glob('../../../live.*') as $dir) {
    $nr = substr(basename($dir),5,1);
    if (substr($dir,0,6) == "live.$version") {
      continue;  
    }
    
    
    $target = '../../../live.'.$nr.'/www/static/'.$nr.'/';
        
    if ($nr != $version && !(is_link($target))) {
        @unlink($nr);
        symlink($target,$nr);
        
        
    }
    //full_copy($dir,$liveVersionPath . '/inc/'.basename($dir));
}
chdir($rootPath);


@unlink('live');
symlink(basename($liveVersionPath),'live');


            
    
    


function full_copy($source, $target) {
    print "Copying " . $source ."\n";
    if (is_dir ( $source )) {
		@mkdir ( $target );
		
		$d = dir ( $source );
		
		while ( FALSE !== ($entry = $d->read ()) ) {
			if ($entry == '.' || $entry == '..' || $entry == '.svn') {
				continue;
			}
			
			$Entry = $source . '/' . $entry;
			if (is_dir ( $Entry )) {
				full_copy ( $Entry, $target . '/' . $entry );
				continue;
			}
			copy ( $Entry, $target . '/' . $entry );
		}
		
		$d->close ();
	} else {
		copy ( $source, $target );
	}

}


