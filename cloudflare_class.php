<?php

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
    * purge_filidentifieres
    */
    public function identifier($domain){
        $result = $this->get_zone($domain);
        if (isset($result->result) && count($result->result) == 1)
           return $result->result[0]->id;

       return false;
    }
    /**
    * purge_files
    */
    public function purge_files($identifier, $files){
        $data = [
            'files' => $files
        ];
        $this->delete('zones/'.$identifier.'/purge_cache', $data);
    }
    /**
    * purge_site
    */
    public function purge_site($identifier){
        $data = [
            'purge_everything' =>  true
        ];
        return $this->delete('zones/'.$identifier.'/purge_cache',$data);
    }
    /**
    * dns_records
    */
    public function dns_records($identifier){
        return $this->get('zones/'.$identifier.'/dns_records',[]);
    }
    /**
    * purge_site
    */
    public function get_zone($name){
        $data = [
            'name'      => $name,
            'status'    => 'active',
            'page'      => 1,
            'match'     => 'all'
        ];
        return $this->get('zones',$data);
    }
    /**
    * purge_site
    */
    public function get_zones(){
        return $this->get('zones',[]);
    }
    //privates below
    /**
    * delete
    */
    private function delete($endpoint,$data){
        return $this->http_request($endpoint,$data,'delete');
    }
    /**
    * post
    */
    private function post($endpoint,$data){
        return $this->http_request($endpoint,$data,'post');
    }
    /**
    * get
    */
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
       

        if ($http_code != 200) {
            //hit error will add in error checking but for now will return back to user to handle
            return json_decode($http_response);
        } else {
            return json_decode($http_response);
        }
    }
}