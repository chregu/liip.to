<?php
/**
 * View which sets text/plain content type headers.
 * @author   Silvan Zurbruegg
 */
class api_views_qr extends api_views_common {
    /**
     * Sends text/plain Content-type
     */
    protected function setHeaders() {
        $this->response->setContentType('image/png');
        parent::setHeaders();
    }
    
    public function dispatch($data, $exceptions = null) {
        if (is_array($data) ) {
             print "<pre>";
             var_dump($data);   
        } else {
            $this->setHeaders();
             qr::generateAsOutput($data,4,'png');  
        
            $this->response->send();
        }
        
    }
    

    
}
