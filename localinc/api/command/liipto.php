<?php
class api_command_liipto extends api_command {
	
    /*
     * The PDO DB Object
     * 
     * @var PDO the PDO object;
     */
    protected  $db = null;
    
	public function __construct($attribs) {
		parent::__construct ( $attribs );
	   $url = $this->request->getParam('url',null);
		if ($url) {
		    $this->url = $url;
		} else {
		  $this->url = $attribs['url'];
		}
		$this->url = trim($this->url);
	}
	
	public function redirect() {
	    $url = $this->getUrlFromCode($this->url);;
	    if ($url) {
	       $this->data = $url;
	    } else {
	        die ("error");
	    } 
	      
	}
	
	public function checkCode() {
	  $this->db =  api_db::factory("default");
	  if ($this->codeExists($this->url)) {
	       $this->data = 'true';
	  } else {
	       $this->data = 'false';
	  }
	}
	
    public function checkCodeReverse() {
      $this->db =  api_db::factory("default");
      $code = $this->getCodeFromDB($this->url);
      if ($code) {
           $this->data = json_encode($code);
      } else {
           $this->data = 'false';
      }
    }

	public function create() {
	    if (empty($this->url)) {
	        die("empty url");
	    }
	    $code = $this->request->getParam('code',null);
	    if ($code) {
	        //normalize code
	       $code = preg_replace("#[^a-zA-Z0-9_]#","",$code);
	   }
       $this->data = 'http://'.$this->request->getHost() . '/'. $this->getShortCode($this->url,$code);
       
	}
	
	protected function getUrlFromCode($code) {
	   if (!$this->db) {
           $this->db =  api_db::factory("default");
        }
        $query = "SELECT url from urls where code = :code";
        $stm = $this->db->prepare($query);
        $stm->execute(array(":code" => $code)); 
        return $stm->fetchColumn();
	}
	
	protected function getShortCode($url,$usercode = null,$lconly = false) {
	    // if no ., it's not a real URL :)
	    if (strpos($url,'.') === false) {
           return $url; 
        }
        $url = $this->normalizeUrl($url);
	    //check if it's an own URL :)
	    $host = 'http://'.$this->request->getHost();
        
	    if (strpos($url,$host) === 0) {
            return substr($url, strlen($host)+1);
	    }
	    
	    if (!$this->db) {
	       $this->db =  api_db::factory("default");
	    }
        $urlmd5 = md5 ( $url );
        
        //check if a code exists
        $code = $this->getCodeFromDBWithMD5($urlmd5);
        //if not create one
        if (!$code) {
            // insert url
            $this->insertUrl($url,$usercode,$lconly,$urlmd5);
            // get code again (if another code with the same url was inserted in the meantime...)
            $code = $this->getCodeFromDBWithMD5($urlmd5);
            
        }
        return $code;
	    
	}
	
	protected function insertUrl($url,$code = null,$lconly = false,$urlmd5 = null) {
	    if (!$urlmd5) {
           $urlmd5 = md5($url);
        }
        if (!$code) {
            $code = $this->getNextCode($lconly);
        }
            
        $query = 'INSERT INTO urls (code,url,md5) VALUES (:code,:url,:urlmd5)';
        
        $stm = $this->db->prepare ( $query );
        
        if (! $stm->execute ( array (':code' => $code, ':url' => $url, ':urlmd5' => $urlmd5 ) )) {
           die("DB Error");
        }
        return $code;
	}
	
	protected function getNextCode($lconly) {
		if ($lconly) {
			$tablename = 'lower';
			$id = $this->nextId ( $tablename );
			$code = $this->id2url ( $id, $lconly );
		} else {
			$tablename = 'mixed';
			$code = $this->id2url ( $this->nextId ( $tablename ), $lconly );
			while ( $code == strtolower ( $code ) ) {
				$code = $this->id2url ( $this->nextId ( $tablename ), $lconly );
			}
		}
		if ($this->codeExists($code)) {
		    $code = $this->getNextCode($lconly);
		}
		
		return $code;
	}
	
	protected function codeExists($code) {
	   $query = "SELECT code from urls where code = " . $this->db->quote($code);
        if ($this->db->query($query)->rowCount() > 0) {
            return true;
        }
        return false;
	}
	
	protected function getCodeFromDB($url) {
	    $urlmd5 = md5($url);
        return $this->getCodeFromDBWithMD5($urlmd5);   
	}
	
	protected function getCodeFromDBWithMD5($urlmd5) {
	    $query = "SELECT code FROM urls where md5 = :urlmd5";
        $stm = $this->db->prepare($query);
        $stm->execute(array(':urlmd5' => $urlmd5));
        return $stm->fetchColumn();
	}
	
	
	protected function normalizeUrl($url) {
	    if (strpos($url,'https:') === 0) {
            $url = preg_replace("#https:/+#","",$url);
            $url = 'https://'.$url;
        } else {
            $url = preg_replace("#http:/+#","",$url);
            $url = 'http://'.$url;
        }
        return $url;
        
        
	}
	
	protected function nextId($name = 'mixed') {

	    $sequence_name = 'ids_'.$name;
        $seqcol_name = 'id';
        $query = "INSERT INTO $sequence_name ($seqcol_name) VALUES (NULL)";
        $this->db->query($query);
        
        $value = $this->db->lastInsertId();
        
        if (is_numeric($value)) {
            $query = "DELETE FROM $sequence_name WHERE $seqcol_name < $value";
            $this->db->query($query);
        }
        return $value;
    }

	
	protected function id2url($val, $lconly = false) {
	    if (0 == $val) {
			return 0;
	    }
		if ($lconly) {
		    $base = 36;
			$symbols = 'adgjmptuwvk0376e9f8b4y2osi5nz1crhxlq';
		} else {
		    $base = 63;
			$symbols = 'JVPAGYRKBWLUTHXCDSZNFOQMEIef02nwy1mdtx7p89653cbaoj4igkvrsqz_hul';
		}
		$result = '';
		$exp = $oldpow = 1;
		while ( $val > 0 && $exp < 10 ) {
			
			$pow = pow ( $base, $exp ++ );
			
			$mod = ($val % $pow);
			// print $mod ."\n";
			$result = substr ( $symbols, $mod / $oldpow, 1 ) . $result;
			$val -= $mod;
			$oldpow = $pow;
		}
		return $result;
	}
	
	public function getData() {
        return $this->data;   
    }
	

}
