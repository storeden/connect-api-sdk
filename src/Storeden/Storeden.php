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
 * Class Storeden
 *
 * @package Storeden
 *
 */
class Storeden{

	/**
	 * @const {String} Version number of the official Storeden Connect API SDK (PHP)
	 */
	const SDK_VERSION = '1.67.b';

	/**
	 * @const {String} Default Connect API version for requests. Can be varied by new Storeden\Storeden($config);
	 */
	const DEFAULT_API_VERSION = 'v1.1';

	const API_KEY = '';

	const API_EXCHANGE = '';

	protected $config;

	public function __construct(array $config = []){

		$config = array_merge(
			[
				'api_key' => static::API_KEY,
				'api_exchange' => static::API_EXCHANGE,
				'api_version' => static::DEFAULT_API_VERSION
			], $config
		);

		if(!$config['api_key']){
			throw new StoredenSDKException('A valid api_key was not supplied in config');
		}
		if(!$config['api_exchange']){
			throw new StoredenSDKException('A valid api_exchange was not supplied in config');
		}

		$this->config = $config;

	}

	public function get($endpoint){
		return $this->api($endpoint, 'GET');
	}

	public function post($endpoint, $params = []){
		return $this->api(
			$endpoint,
			'POST',
			$params
		);
	}

	public function put($endpoint, $params = []){
		return $this->api(
			$endpoint,
			'PUT',
			$params
		);
	}

	public function delete($endpoint, $params = []){
		return $this->api(
			$endpoint,
			'DELETE',
			$params
		);
	}

	public function upload($endpoint, $filename, $params = []){

		if(!file_exists($filename)){
			throw new StoredenSDKException('Filename: '.$filename.' must exists ');
		}

		$params['file'] = curl_file_create($filename);

		return $this->api(
			$endpoint,
			'UPLOAD',
			$params
		);

	}

	public function api($endpoint, $method, $params = [], $callback = ''){

		try{
			$http_client = new StoredenHTTPClient();
			$http_client->prepareConnection(
				'/'.static::DEFAULT_API_VERSION.$endpoint,
				$method,
				$params,
				['key' => $this->config['api_key'], 'exchange' => $this->config['api_exchange'] ]
			);

			$http_client->execConnection();

			list($response_header, $response_body) = $http_client->getResponse();

			$http_client->closeConnection();

			$_response = new StoredenConnectResponse($response_header, $response_body);

			return $_response;

		}catch(StoredenSDKException $e){
			if(php_sapi_name() === 'cli'){
				echo '[ERROR] Prerequisites not met'.PHP_EOL;
				exit;
			}

		}

	}
}
