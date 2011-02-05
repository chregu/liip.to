<?php

class UrlCheck {

    public  $reason = array();
    static public $doubleCcTld = null;
    static public $whitelist = null;

    function __construct()  {

    }


    public function isListed($url, $ip = null, $all = false) {

        $this->reason = array();
        if ($url) {
	        if ($this->isOnWhitelist($url)) {
			return true;
		}
            $url = str_replace(" ","%20",$url);
            $this->checkUrl($url, $all);

            if (!$all && count($this->reason) > 0) {
                return true;
            }



            $resp = $this->findFinalURL($url);
            if ($resp['http_code'] >= 300) {
                    $this->reason['httperror'] =  $resp['http_code'] . " HTTP code for " . $resp['url'] ;
            }
            $finalURL = $resp['url'];

            if ($finalURL && $finalURL != $url) {
                $this->checkUrl($finalURL, $all);
            }

        }

        if ($ip) {
            $this->checkIp($ip,$all);
            if (!$all && count($this->reason) > 0) {
                return true;
            }

        }

        if (count($this->reason) > 0) {
            return true;
        }
	return false;
    }

    protected function checkUrl($url, $all) {
        if ($this->isOnDBL_Spamhaus($url) && !$all) {
            return true;
        }
        if ($this->isOnSurbl($url) && !$all) {
            return true;
        }

        $parsed_uri = parse_url($url);
        $host       = $parsed_uri['host'];
        if ($host) {
            $urlip = gethostbyname($host);
            if ($urlip) {
                $this->checkIp($urlip,$all);
                if (!$all && count($this->reason) > 0) {
                    return true;
                }
            }
        }


    }

    protected function checkIp($ip,$all) {
        if ($this->isOnSpamhaus($ip) && !$all) {
            return true;
        }


    }

    protected function isOnSpamhaus($ip) {
        $revip = $this->reverseIp($ip);
        $server = "sbl-xbl.spamhaus.org";
        $name = $revip . "." . $server;

        if ($name == ($resp = gethostbyname($name))) {
            return false;
        } else {
            $this->reason[] = "$ip is on $server with $resp";
            return $resp;

        }


    }

    protected function isOnSurbl($url) {
        return $this->isOnDNSBL($url, "multi.surbl.org");
    }

    protected function isOnDBL_Spamhaus($url) {
        return $this->isOnDNSBL($url, "dbl.spamhaus.org");
    }

    protected function isOnDNSBL($url,$server) {
        $url = $this->getHostForLookup($url);
        if ($url.".".$server == ($ip = gethostbyname($url.".".$server))) {
            return false;
        } else {
            if ($ip == "127.0.1.255") {
		return false;
	    }
            $this->reason[] = "$url is on $server with $ip";
            return $ip;
        }
    }

    function reverseIp($ip)
    {
        return implode('.', array_reverse(explode('.', $ip)));
    }

    /**
    * Get Hostname to ask for.
    *
    * Performs the following steps:
    *
    * (1) Extract the hostname from the given URI
    * (2) Check if the "hostname" is an ip
    * (3a) IS_IP Reverse the IP (1.2.3.4 -> 4.3.2.1)
    * (3b) IS_FQDN Check if is in "CC-2-level-TLD"
    * (3b1) IS_IN_2LEVEL: we want the last three names
    * (3b2) IS_NOT_2LEVEL: we want the last two names
    * (4) return the FQDN to query.
    *
    * @param string $uri       URL to check.
    * @param string $blacklist Blacklist to check against.
    *
    * @access protected
    * @return string Host to lookup
    */
    function getHostForLookup($uri)
    {
        // (1) Extract the hostname from the given URI
        $host       = '';
        $parsed_uri = parse_url($uri);
        $host       = $parsed_uri['host'];
        // (2) Check if the "hostname" is an ip
        /*  if (Net_CheckIP::check_ip($host)) {
            // (3a) IS_IP Reverse the IP (1.2.3.4 -> 4.3.2.1)
            $host = $this->reverseIp($host);
        } else {*/
            $host_elements = explode('.', $host);
            while (count($host_elements) > 3) {
                array_shift($host_elements);
            } // while
            $host_3_elements = implode('.', $host_elements);

            $host_elements = explode('.', $host);
            while (count($host_elements) > 2) {
                array_shift($host_elements);
            } // while
            $host_2_elements = implode('.', $host_elements);

            // (3b) IS_FQDN Check if is in "CC-2-level-TLD"
            if ($this->isDoubleCcTld($host_2_elements)) {
                // (3b1) IS_IN_2LEVEL: we want the last three names
                $host = $host_3_elements;
            } else {
                // (3b2) IS_NOT_2LEVEL: we want the last two names
                $host = $host_2_elements;
            } // if
      //  } // if
      // (4) return the FQDN to query
      return $host;
    } // function


    function isDoubleCcTld($fqdn)
    {
        self::initDoubleCcTld();
        // 30 Days should be way enough
        if (array_key_exists($fqdn, self::$doubleCcTld)) {
            return true;
        } else {
            return false;
        } // if
    } // function


    function initDoubleCcTld() {

        if (!self::$doubleCcTld) {
            // from  http://george.surbl.org/two-level-tlds
            $data = file_get_contents(dirname(__FILE__).'/two-level-tlds.dat');
            $data = explode("\n", trim($data));
            self::$doubleCcTld = array_flip($data);
        }
    }

    function isOnWhitelist($url) {

         self::initWhitelist();
 	$host       = '';
        $parsed_uri = parse_url($url);
        $host       = $parsed_uri['host'];
          if (array_key_exists($host, self::$whitelist)) {
            return true;
        } else {
            return false;
        }
    }
    function initWhitelist() {

        if (!self::$whitelist) {
            $data = file_get_contents(dirname(__FILE__).'/whitelist.dat');
            $data = explode("\n", trim($data));
            self::$whitelist = array_flip($data);
        }
    }


    function findFinalURL($url) {
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	$f = fopen("/dev/null","w");
	curl_setopt( $ch, CURLOPT_FILE, $f);
        curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 20 );


        curl_exec( $ch );
        $response = curl_getinfo( $ch );
        $url = $response['url'];
        curl_close ( $ch );
	fclose($f);
        return array("url" =>$url, "http_code" => $response['http_code']);

    }


}
