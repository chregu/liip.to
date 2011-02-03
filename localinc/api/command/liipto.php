<?php
class api_command_liipto extends api_command {

    /*
     * The PDO DB Object
     *
     * @var PDO the PDO object;
     */
    protected $db = null;
    protected $attribs = null;

    public function __construct($attribs) {
        parent::__construct($attribs);
        $this->attribs = $attribs;

        $url = $this->request->getParam('url', null);
        if ($url) {
            $this->url = $url;
            if (ini_get('magic_quotes_gpc')) {
                $this->url = stripslashes($this->url);
            }
        } else {
            $this->url = $attribs['url'];
        }
        $this->url = trim($this->url);
    }

    public function redirect() {
        if (substr($this->url, -1) == "-") {
            $this->response->redirect(API_WEBROOT . "api/resolve/" . substr($this->url, 0, -1));
        }
        $code = $this->getUrlFromCode($this->url);
    
        if ($code) {
            $this->data = $code;
        } else {
            die("error");
        }
        /**
         new version from bootcamp
        $counter_sql = 1;
    
        $query = 'UPDATE urls SET counter = counter + 1 WHERE url = :url';
    
        $stm = $this->db->prepare($query);

        if (!$stm->execute(array(
            ':url' => $code,
        ))) {
            throw new api_exception('DB Error');
        }
        */
    }

    public function resolve() {
        $this->db = api_db::factory("default");
        $this->data = $this->getUrlFromCode($this->url);
    }

    public function checkCode() {
        $this->db = api_db::factory("default");
        if ($this->codeExists($this->url)) {
            $this->data = 'true';
        } else {
            $this->data = 'false';
        }
    }

    public function checkCodeReverse() {
        $this->db = api_db::factory("default");
        $code = $this->getCodeFromDB($this->url);
        if ($code) {
            $this->data = json_encode($code);
        } else {
            $this->data = 'false';
        }
    }

    public function checkCodeReverseAndRevCan() {
        $this->checkCodeReverse();
        $this->data = json_encode(array("alias" => json_decode( $this->data),"revcan" => $this->getRevCanonical($this->url)));
    }

    public function create() {
        if (empty($this->url)) {
            die("empty url");
        }
        if (strpos($this->url,"sitemarks.in")) {
            $this->data = $this->url;
	    return;
        }

        if ($rej = $this->checkBlacklists($this->url)) {
		error_log("SPAM from " . $_SERVER['REMOTE_ADDR'] . " " . $rej . ":  ". $this->url);
                $this->data = $this->url;
		return;
	}

        $code = $this->request->getParam('code', null);
        if ($code) {
            //normalize code
            $code = preg_replace("#[^a-zA-Z0-9_]#", "", $code);
        } else if ($revcan = $this->getRevCanonical($this->url)) {
               $this->data = $revcan;
               return;
        }
        $this->data = $this->getFullShortUrl($this->url,$code);
        return $this->data;
    }
    
    public function result() {
        $url = $this->create();
    
        $this->data = array();
        $this->data[] = array("url" => $url);
    }

   protected function getFullShortUrl($url, $code = null) {
	$code =  $this->getShortCode($url, $code);
        if (!$code) {
     	    return  $this->getGoogleCode($url);
        } else {
            return 'http://' . $this->request->getHost() . '/' . $code;
        }


   }

    public function create140() {
        if (empty($this->url)) {
            die("empty url");
        }

        $url = $this->url;
        $text = $this->request->getParam('text', '');

        if (ini_get('magic_quotes_gpc')) {
            $text = stripslashes($text);
        }
        
        $maxChars = $this->request->getParam('maxchars', '125');

        if (strlen($text) > 140) {
            $text = substr($text, 0, $maxChars);
        }
        //if there's a shorturl defined by the site, always use this
        if ($revcan = $this->getRevCanonical($url)) {
                 return $this->trimTo140($revcan, $text, $maxChars);
        }
        //if the long url fits into 140chars, use this
        if (strlen($text . $url) <= $maxChars) {
            return $this->returnCreate140($url, $text, $maxChars);
        }
        if ($rej = $this->checkBlacklists($url)) {
		error_log("SPAM from " . $_SERVER['REMOTE_ADDR'] . " " . $rej . ":  ". $this->url);
	} else {
        //otherwise generate a shorturl and use it.
	        $url = $this->getFullShortUrl($url);
        }

        return $this->trimTo140($url, $text, $maxChars);
    }

    protected function trimTo140($url, $text, $maxChars) {

        if (strlen($text . $url) <= 140) {
            return $this->returnCreate140($url, $text);
        } else {
            // if we shorten the text, we shorten it to $maxChars to allow RTs
            $text = substr($text, 0, $maxChars - strlen($url) - 1);
            return $this->returnCreate140($url, $text);
        }
    }

    protected function returnCreate140($url, $text) {
        $this->data = json_encode(array(
                "url" => $url,
                "text" => $text
        ));

        return true;
    }

    public function createFromPath() {
        $this->url = preg_replace(array(
                '%(.+):/(?!/)%'
        ), array(
                '$1://'
        ), $this->url);
        
        $this->create();
    }

    public function revcan() {
        if (empty($this->url)) {
            die("empty url");
        }
        
        $this->data = $this->getRevCanonical($this->url);
    }

    protected function getRevCanonical($url) {

        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, "http://revcanonical.appspot.com/api?url=" . urlencode($this->url));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // grab URL and pass it to the browser
        $data = curl_exec($ch);

        $respCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // close cURL resource, and free up system resources
        curl_close($ch);

        if ($respCode != 200) {
            return false;
        } else if ($data != $url) {
            return $data;
        } else {
            return false;
        }
    }

    protected function getUrlFromCode($code) {
        if (!$this->db) {
            $this->db = api_db::factory("default");
        }
        $query = "SELECT url from urls where code = :code";
        $stm = $this->db->prepare($query);
        $stm->execute(array(
                ":code" => $code
        ));
        return $stm->fetchColumn();
    }

    protected function getShortCode($url, $usercode = null, $lconly = false) {
        if (strpos($url,"migipedia")) {
            $lconly = true;
        }
        // if no ., it's not a real URL :)
        if (strpos($url, '.') === false) {
            return $url;
        }
        $url = $this->normalizeUrl($url);
        //check if it's an own URL :)
        $host = 'http://' . $this->request->getHost();

        if (strpos($url, $host) === 0) {
            return substr($url, strlen($host) + 1);
        }

        if (!$this->db) {
            $this->db = api_db::factory("default");
        }
        $urlmd5 = md5($url);

        //check if a code exists
        $code = $this->getCodeFromDBWithMD5($urlmd5);
        //if not create one
        if (!$code) {
        $private = (bool)$this->request->getParam('checkbox', false);
            // insert url
            $this->insertUrl($url, $usercode, $lconly, $urlmd5, $private);
            // get code again (if another code with the same url was inserted in the meantime...)
            $code = $this->getCodeFromDBWithMD5($urlmd5);

        }
        return $code;
    }

    protected function insertUrl($url, $code = null, $lconly = false, $urlmd5 = null, $private = false) {
        if (!$urlmd5) {
            $urlmd5 = md5($url);
        }

        if ($code && $this->codeExists($code)) {
            $code = $this->getNextCode($lconly);
        }

        if (!$code) {
            $code = $this->getNextCode($lconly);
        }
//ANTI SPAM SCHUTZ, remove, wenn wir was besseres haben
//return null;

        $query = 'INSERT INTO urls (code,url,md5,IP) VALUES (:code,:url,:urlmd5,:IP)';
        $private_sql = $private ? 1 : 0;
        /*
        new version from bootcamp branch
        $query = 'INSERT INTO urls (code,url,md5,private) VALUES (:code,:url,:urlmd5,:private)';
        */ 
        

        $stm = $this->db->prepare($query);

        if (!$stm->execute(array(
                ':code' => $code,
                ':url' => $url,
                ':urlmd5' => $urlmd5,
                ':IP' => $_SERVER['REMOTE_ADDR']
                //':private' => $private_sql
        ))) {
            throw new api_exception('DB Error');
        }
        return $code;
    }

    protected function getNextCode($lconly) {
        if ($lconly) {
            $tablename = 'lower';
            $id = $this->nextId($tablename);
            $code = $this->id2url($id, $lconly);
        } else {
            $tablename = 'mixed';
            $code = $this->id2url($this->nextId($tablename), $lconly);
        }
        if ($this->codeExists($code)) {
            $code = $this->getNextCode($lconly);
        }

        return $code;
    }

    protected function codeExists($code) {
        $query = "SELECT count(code) from urls where code = " . $this->db->quote($code);
        $res = $this->db->query($query);
        if (!$res) {
            $info =  $this->db->errorInfo();
            throw new api_exception_Db(api_exception::THROW_FATAL,array(),0,$info[2]);
        }
        $r = $res->fetch();
        if ($r && $r[0] > 0) {
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
        $stm->execute(array(
                ':urlmd5' => $urlmd5
        ));
        return $stm->fetchColumn();
    }

    protected function normalizeUrl($url) {
        if (strpos($url, 'https:') === 0) {
            $url = preg_replace("#https:/+#", "", $url);
            $url = 'https://' . $url;
        } else {
            $url = preg_replace("#http:/+#", "", $url);
            $url = 'http://' . $url;
        }
        return $url;
    }

    protected function nextId($name = 'mixed') {
        $sequence_name = 'ids_' . $name;
        $seqcol_name = 'id';
        $query = "INSERT INTO $sequence_name ($seqcol_name) VALUES (NULL)";
        $res = $this->db->exec($query);
        if (!$res) {
            $info =  $this->db->errorInfo();
            throw new api_exception_Db(api_exception::THROW_FATAL,array(),0,$info[2]);
        }

        $value = $this->db->lastInsertId($seqcol_name);

        if (!$value) {
           throw new api_exception(api_exception::THROW_FATAL,array(),0,"Couldn't get a value for nextId");
        }

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
        /*
        if ($lconly) {
            $base = 36;
            $symbols = 'adgjmptuwvk0376e9f8b4y2osi5nz1crhxlq';
        } else {
            */
            $base = 63;
            $symbols = 'JVPAGYRKBWLUTHXCDSZNFOQMEIef02nwy1mdtx7p89653cbaoj4igkvrsqz_hul';
        //}
        $result = '';
        $exp = $oldpow = 1;
        while ($val > 0 && $exp < 10) {

            $pow = pow($base, $exp++);

            $mod = ($val % $pow);
            // print $mod ."\n";
            $result = substr($symbols, $mod / $oldpow, 1) . $result;
            $val -= $mod;
            $oldpow = $pow;
        }
        return $result;
    }

    public function search(){
        if (empty($this->url)) {
        die("empty input");
        }
    
        $input = $this->request->getParam('url', null);
        
        $url = $this->getUrlFromSearch($input);
        
        $this->data["input"] = $this->request->getParam('url', "error");
        $this->data["search"] = $url;
    }
    
    public function getUrlFromSearch($parturl) {
    if (!$this->db) {
        $this->db = api_db::factory("default");
    }
    $query = "SELECT * FROM urls WHERE url LIKE :parturl OR code LIKE :parturl";
    $stm = $this->db->prepare($query);
    $stm->execute(array(
        'parturl' => "%".$parturl."%"
    ));
    
    $results = $stm->fetchAll(PDO::FETCH_ASSOC);
    return $results;
    }

    public function getData() {
        return $this->data;
    }

   protected function getGoogleCode($url) {
    
        $ch = curl_init();      

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/urlshortener/v1/url");
    
        curl_setopt($ch, CURLOPT_POST, 0);     
	 curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode(array("longUrl" => $url)));

        curl_setopt($ch, CURLOPT_HEADER, 0);     
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        // grab URL and pass it to the browser
        $data = curl_exec($ch);

        $respCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // close cURL resource, and free up system resources
        curl_close($ch);    
        $data  = json_decode($data,true);
        return $data['id'];

   }

protected function checkBlacklists($url) {
        $surbl = new Net_DNSBL_SURBL();
        if ($surbl->isListed("http://liip.ch/")) {
        } else {
                if ($surbl->isListed($url)) {
			return "surbl.org blacklisted";
                }
        }

   	$blacklist = "xbl.spamhaus.org";

	$ip = $_SERVER['REMOTE_ADDR'];
        $d = new Net_DNSBL();
        $d->setBlacklists(array($blacklist));

        if ($d->isListed($ip)) {
            return "xbl.spamhaus.org blacklisted sender IP ($ip <http://www.spamhaus.org/query/bl?ip=$ip>). ";
        }
	return false;
            

}

}
