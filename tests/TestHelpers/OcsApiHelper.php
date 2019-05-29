<?php
/**
 * ownCloud
 *
 * @author Artur Neumann <artur@jankaritech.com>
 * @copyright Copyright (c) 2017 Artur Neumann artur@jankaritech.com
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License,
 * as published by the Free Software Foundation;
 * either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace TestHelpers;

use Codeception\Module\Cli;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Batch\Batch;
use GuzzleHttp\BatchRequestTransfer;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;

/**
 * Helper to make requests to the OCS API
 *
 * @author Artur Neumann <artur@jankaritech.com>
 *
 */
class OcsApiHelper {
	/**
	 * @param string $baseUrl
	 * @param string $user if set to null no authentication header will be sent
	 * @param string $password
	 * @param string $method HTTP Method
	 * @param string $path
	 * @param array $body array of key, value pairs e.g ['value' => 'yes']
	 * @param int $ocsApiVersion (1|2) default 2
	 * @param array $headers
	 *
	 * @return ResponseInterface
	 */
	public static function sendRequest(
		$baseUrl, $user, $password, $method, $path, $body = [], $ocsApiVersion = 2, $headers = []
	) {
		$fullUrl = $baseUrl;
		if (\substr($fullUrl, -1) !== '/') {
			$fullUrl .= '/';
		}
		$fullUrl .= "ocs/v{$ocsApiVersion}.php" . $path;

		return HttpRequestHelper::sendRequest($fullUrl, $method, $user, $password, $headers, $body);
	}

	public static function sendBatchRequest(
		$baseUrl, $user, $password, $method, $path, $body = [], $ocsApiVersion = 2, $headers = []
	) {
		$client = new Client();
		$fullUrl = $baseUrl;
		if (\substr($fullUrl, -1) !== '/') {
			$fullUrl .= '/';
		}
		$fullUrl .= "ocs/v{$ocsApiVersion}.php" . $path;
		$requests = [];
		foreach ($body as $b) {
			var_dump($b);
			$request = HttpRequestHelper::createRequest($fullUrl, $method, $user, $password, $headers, $b);
			array_push($requests, $request);
		}
		$responses = Pool::batch($client, $requests);
		var_dump($responses->getResult($requests[0]));
		return $responses;
	}
}
