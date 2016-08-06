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

    public function purge_files($domain, $files){
        $data = [
            'files' => $files
        ];
        $this->delete('zones/'.$domain.'/purge_cache', $data);
    }

    private function delete($endpoint,$data){
        return $this->http_request($endpoint,$data,'delete');
    }
    /**
     * Handle http request to cloudflare server
     */
    private function http_request($endpoint,$data, $method)
    {
        //setup url
        $url = $this->url.$endpoint;


        //headers set
        $headers = ["X-Auth-Email: {$this->auth_email}", "X-Auth-Key: {$this->auth_key}"];
        $headers[] = 'Content-type: application/json';

        //json encode data
        $json_data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_URL, self::$URL[$type]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'post') 
            curl_setopt($ch, CURLOPT_POST, true);

        if ($method === 'put') 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        if ($method === 'delete') 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');


        
        //get request
        if (!isset($method) || $method == 'get')
            $url .= '?'.http_build_query($data);
        
        //add headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);


        $http_result = curl_exec($ch);
        $error       = curl_error($ch);
        $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200) {
            return array(
                'error' => $error
            );
        } else {
            return json_decode($http_result);
        }
    }
}