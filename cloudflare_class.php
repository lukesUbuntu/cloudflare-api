<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);
class cloudflare_api
{
    
    
    //Timeout for the API requests in seconds
    const TIMEOUT = 5;
    
    private $URL = 'https://api.cloudflare.com/client/v4/';
    //Stores the email login
    private $auth_email;
    private $auth_key;
    
    
    public function __construct()
    {
        
        $num_args = func_num_args();
        if ($num_args == 2){
            $parameters = func_get_args();
            $this->auth_email     = $parameters[0];
            $this->auth_key = $parameters[1];
        }else{
            //throw error
        }
        
    }
    
    /**
    * purge_files
    */
    public function purge_files($domain, $files){
        $data = [
        'files' => $files
        ];
        $this->delete('zones/'.$domain.'/purge_cache', $data);
    }
    /**
    * purge_site
    */
    public function purge_site($domain){
        $this->delete('zones/'.$domain.'/purge_cache');
    }

    public function get_zone($name){
        $data = [
            'name'      => $name,
            'status'    => 'active',
            'page'      => 1,
            'match'     => 'all'
        ];
        return $this->get('zones',$data);
    }
    public function get_zones(){
        return $this->get('zones',[]);
    }
    private function delete($endpoint,$data){
        return $this->http_request($endpoint,$data,'delete');
    }
    private function get($endpoint,$data){
        return $this->http_request($endpoint,$data,'get');
    }
    /**
    * Handle http request to cloudflare server
    */
    private function http_request($endpoint,$data, $method)
    {
        //setup url
        $url = $this->URL.$endpoint;
        
        //echo $url;exit;
        
        //headers set
        $headers = ["X-Auth-Email: {$this->auth_email}", "X-Auth-Key: {$this->auth_key}"];
        $headers[] = 'Content-type: application/json';
        
        //json encode data
        $json_data = json_encode($data);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        if ($method === 'post')
            curl_setopt($ch, CURLOPT_POST, true);
        
        if ($method === 'put')
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        
        if ($method === 'delete')
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        
    
        //get request otherwise pass post data
        if (!isset($method) || $method == 'get')
            $url .= '?'.http_build_query($data);
        else
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        //echo $url;exit;

        //add headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        
        
        $http_response = curl_exec($ch);
        $error       = curl_error($ch);
        $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        //print_r($http_result);

        if ($http_code != 200) {
            return array(
            'error' => $error
            );
        } else {
            return json_decode($http_response);
        }
    }
}