<?php

/**
 * Copyright 2016 Projectmoon, SRL.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Storeden. More information about policies can be found here:
 * [https://developers.storeden.com/docs/apps/policy]
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

if (!function_exists('curl_init')) {
  throw new Exception('Storeden Connect API needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Storeden Connect API needs the JSON PHP extension.');
}

class StoredenConnectAPI {

    const VERSION = '1.0-dev';

    private $api_version = 'v1.1';

    private $base_path = NULL;

    private $key = NULL;

    private $exchange = NULL;

    private $curl_opts = array(
      CURLOPT_CONNECTTIMEOUT => 5,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_TIMEOUT        => 20,
      CURLOPT_VERBOSE        => FALSE,
      CURLOPT_SSLVERSION     => CURL_SSLVERSION_DEFAULT,
      CURLOPT_USERAGENT      => 'storeden-php-1.1b',
    );

    public function __construct($options){

        if(!isset($options['key'])){
            throw new Exception('StoredenConnectSDK need your Connect key');
        }

        if(!isset($options['key'])){
            throw new Exception('StoredenConnectSDK need your Connect exchange key');
        }

        $this->key = (string)$options['key'];

        $this->exchange = (string)$options['exchange'];

        $curl_options = $this->curl_opts;

        $curl_options[CURLOPT_HTTPHEADER] = array(
            'key: '.$this->key,
            'exchange: '.$this->exchange,
            'Expect: '
        );

        $this->curl_opts = $curl_options;

        $this->base_path = 'https://connect.storeden.com/'.$this->api_version;

    }

    private function __parse_response($curl_output){

        if($curl_output == NULL)
            throw new Exception('Invalid response from API');

        $content = json_decode($curl_output);

        return $content;

    }

    /**
     * Make an API call
     **/

    public function api($uri, $type = 'GET', $params = array(), $callback = NULL){

        $curl = curl_init();
        $curl_opts = $this->curl_opts;

        if($type != 'GET'){
            $curl_opts[CURLOPT_CUSTOMREQUEST] =  $type;
            $curl_opts[CURLOPT_POSTFIELDS] =  http_build_query($params);
        }

        $curl_opts[CURLOPT_URL] = $this->base_path.$uri;

        curl_setopt_array($curl, $curl_opts);

        $curl_response = curl_exec($curl);

        curl_close($curl);

        if ($callback == NULL){
            return $this->__parse_response($curl_response);
        }else{
            return call_user_func($callback, $this->__parse_response($curl_response));
        }

    }
}
?>
