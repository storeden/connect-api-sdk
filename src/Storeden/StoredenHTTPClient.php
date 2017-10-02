<?php
/**
 * Copyright 2017 Projectmoon S.R.L.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Storeden;

/**
 * Class StoredenHTTPClient
 *
 * Curl Wrapper Toolkit
 */
class StoredenHTTPClient {

	const CONNECT_API_URL = 'https://connect.storeden.com';

	const DEFAULT_REQUEST_TIMEOUT = 60;

	protected $httpClient;

	protected $curlErrorCode = 0;

	protected $curlErrorMessage = '';

	protected $rawMessage;

	protected $storedenCurlInstance;

	public function __construct(){

		if(!extension_loaded('curl')){
			throw new StoredenSDKException('You must have curl library installed to perform Connect API operations');
		}

		if(!function_exists('json_decode')){
			throw new StoredenSDKException('You must have json_decode library installed to perform Connect API operations');
		}

	}

	public function init(){
		$this->storedenCurlInstance = curl_init();
	}

	public function setSingleOption($key, $value){
		curl_setopt($this->storedenCurlInstance, $key, $value);
	}


	public function setArrayOptions(array $options){
		curl_setopt_array($this->storedenCurlInstance, $options);
	}

	public function execConnection(){
		$this->rawMessage = curl_exec($this->storedenCurlInstance);
	}

	public function getErrno(){
		return curl_errno($this->storedenCurlInstance);
	}

	public function getError(){
		return curl_error($this->storedenCurlInstance);
	}

	public function getAllInfos(){
		return curl_getinfo($this->storedenCurlInstance);
	}

	public function getCurlVersion(){
		return curl_version();
	}

	public function prepareConnection($url, $method, $params, array $headers){

		$options = [
			CURLOPT_URL => static::CONNECT_API_URL.$url,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => static::DEFAULT_REQUEST_TIMEOUT,
			CURLOPT_HEADER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => $this->prepareRequestHeader($headers)
		];

		if($method !== 'GET'){
			$options[CURLOPT_POSTFIELDS] = http_build_query($params);
		}

		$this->init();
		$this->setArrayOptions($options);

	}

	private function prepareRequestHeader(array $headers){

		$curl_headers = [];

		foreach($headers as $header_key => $header_value){
			$curl_headers[] = $header_key.': '.$header_value;
		}

		return $curl_headers;
	}

	public function closeConnection(){
		curl_close($this->storedenCurlInstance);
	}

	public function getResponse(){

		$_response = explode("\r\n\r\n", $this->rawMessage);

		$response_body = array_pop($_response);

		$response_headers = implode("\r\n\r\n", $_response);

		return array(
			trim($response_headers),
			trim($response_body)
		);
	}
}
