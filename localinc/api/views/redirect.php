<?php
/**
 * View which sets text/plain content type headers.
 * @author   Silvan Zurbruegg
 */
class api_views_redirect extends api_views_common {
    /**
     * Sends text/plain Content-type
     */
    protected function setHeaders() {
        parent::setHeaders();
        $this->response->setContentType('text/plain');
    }
    
    public function dispatch($data, $exceptions = null) {
        $this->response->redirect($data);   
    }
    
    
}
