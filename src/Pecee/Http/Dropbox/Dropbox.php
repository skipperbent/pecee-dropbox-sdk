<?php
namespace Pecee\Http\Dropbox;

use Pecee\Http\HttpResponse;
use Pecee\Http\Rest\RestBase;

class Dropbox extends RestBase
{

	protected $serviceUrls = [
		'files/delete'   => 'https://api.dropboxapi.com/2/',
		'files/upload'   => 'https://content.dropboxapi.com/2/',
		'files/download' => 'https://content.dropboxapi.com/2/',
	];

	protected $accessToken;
	protected $args = [];

	public function __construct($accessToken)
	{
		parent::__construct();
		$this->accessToken = $accessToken;

		$this->httpRequest->setReturnHeader(false);
	}

    /**
     * @param string $contents
     * @param string $destination
     * @param array $params
     * @return array
     * @throws DropboxException
     * @throws \Pecee\Http\Rest\RestException
     */
	public function upload($contents, $destination, array $params = [])
	{
		$this->args = array_merge([
			'path'       => $destination,
			'mode'       => 'overwrite',
			'autorename' => false,
			'mute'       => true,
		], $params);

		$this->httpRequest->setContentType('application/octet-stream');
		$this->httpRequest->setRawPostData($contents);

		$response = $this->api('files/upload', static::METHOD_POST);

		if ($response->getStatusCode() !== 200) {
			throw new DropboxException('Failed to upload file: ' . $destination, 500, $response);
		}

		return json_decode($response->getResponse(), true);
	}

    /**
     * @param string $path
     * @return array
     * @throws DropboxException
     * @throws \Pecee\Http\Rest\RestException
     */
	public function delete($path)
	{
		$this->httpRequest->setContentType('application/json');

		$response = $this->api('files/delete', static::METHOD_POST, [
			'path' => $path,
		]);

		if ($response->getStatusCode() !== 200) {
			throw new DropboxException('Failed to delete file: ' . $path, 500, $response);
		}

		return json_decode($response->getResponse(), true);
	}

    /**
     * @param string $path
     * @return array
     * @throws DropboxException
     * @throws \Pecee\Http\Rest\RestException
     */
	public function download($path)
	{
		$this->args = [
			'path' => $path,
		];

		$response = $this->api('files/download');

		if ($response->getStatusCode() !== 200) {
			throw new DropboxException('Failed to download file: ' . $path, 500, $response);
		}

		$output = ['image' => $response->getResponse()];

		$header = $response->getHeader('dropbox-api-result');

		if ($header !== null) {
			$output += json_decode($header, true);
		}

		return $output;
	}

	/**
	 * @param null $url
	 * @param string $method
	 * @param array $data
	 * @return HttpResponse
	 * @throws \Pecee\Http\Rest\RestException
	 */
	public function api($url = null, $method = self::METHOD_GET, array $data = [])
	{
		$this->serviceUrl = $this->serviceUrls[$url];

		$headers = ['Authorization: Bearer ' . $this->accessToken];

		if (count($this->args) > 0) {
			$headers[] = 'Dropbox-API-Arg: ' . json_encode($this->args);
		}

		$this->httpRequest->setHeaders($headers);

		$response = parent::api($url, $method, $data);

		$this->httpRequest->reset();

		return $response;

	}

	public function getAccessToken()
	{
		return $this->accessToken;
	}

}