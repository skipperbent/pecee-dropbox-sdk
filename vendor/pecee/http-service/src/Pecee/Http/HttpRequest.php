<?php

namespace Pecee\Http;

class HttpRequest
{
	protected $url;
	protected $method;
	protected $headers;
	protected $options;
	protected $rawData;
	protected $data;
	protected $timeout;
	protected $returnHeader;
	protected $contentType;

	public function __construct($url = null)
	{

		if (!function_exists('curl_init')) {
			throw new \Exception('This service requires the CURL PHP extension.');
		}

		// Ensure no PHP timeout
		set_time_limit(0);

		$this->reset();
		$this->url = $url;
	}

	public function reset()
	{
		$this->url = null;
		$this->options = [];
		$this->headers = [];
		$this->data = [];
		$this->rawData = null;
		$this->method = null;
		$this->returnHeader = true;
		$this->contentType = null;
	}

	public function addHeader($header)
	{
		$this->headers[] = $header;
	}

	public function setHeaders(array $headers)
	{
		$this->headers = $headers;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function addOption($option, $value)
	{
		$this->options[$option] = $value;
	}

	public function setOptions(array $options)
	{
		$this->options = $options;
	}

	public function addPostData($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function setPostData(array $data)
	{
		$this->data = $data;
	}

	public function setRawPostData($data)
	{
		$this->rawData = $data;
	}

	public function getPostData()
	{
		return $this->data;
	}

	public function getRawPostData()
	{
		return $this->rawData;
	}

	public function post($return = false)
	{
		$this->options[CURLOPT_POST] = true;
		$this->execute($return);
	}

	public function get($return = false)
	{
		// Alias for execute
		$this->execute($return);
	}

	public function setTimeout($timeout)
	{
		$this->timeout = $timeout;
	}

	public function setMethod($method)
	{
		$this->method = $method;
	}

	public function setContentType($contentType) {
		$this->contentType = strtolower($contentType);
		return $this;
	}

	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function setReturnHeader($bool)
	{
		$this->returnHeader = $bool;

		return $this;
	}

	public function getReturnHeader()
	{
		return $this->returnHeader;
	}

	/**
	 * Set basic authentication
	 *
	 * @param $username
	 * @param $password
	 */
	public function setBasicAuth($username, $password)
	{
		$this->addHeader('Authorization: Basic ' . base64_encode(sprintf('%s:%s', $username, $password)));
	}

	public function execute($return = true)
	{
		$handle = curl_init();

		if ($this->url === null) {
			throw new \InvalidArgumentException('Missing required property: url');
		}

		if (strtolower($this->method) !== 'get') {
			$this->url .= ((strpos($this->url, '?') === false) ? '?' : '&');
		}

		curl_setopt($handle, CURLOPT_URL, $this->url);

		$response = new HttpResponse($handle);

		if($this->contentType !== null) {
			$this->addHeader('Content-Type: ' . $this->contentType);
		}

		if ($this->returnHeader) {
			curl_setopt($handle, CURLOPT_HEADER, true);
			curl_setopt($handle, CURLOPT_HEADERFUNCTION, [&$response, 'parseHeader']);
		}

		if ($return) {
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		}

		if ($this->timeout) {
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, $this->timeout);
			curl_setopt($handle, CURLOPT_TIMEOUT_MS, $this->timeout);
		}

		// Add request data
		if ($this->method && strtolower($this->method) !== 'get') {

			switch($this->contentType) {
				default:
					$data = $this->rawData;
					break;
				case 'application/json':
					$data = json_encode($this->data);
					break;
				case 'application/x-www-form-urlencoded':
					$data = http_build_query($this->data);
					break;
			}

			foreach ($this->headers as $key => $header) {
				if (stripos($header, 'content-length:') !== false) {
					unset($this->headers[$key]);
				}
			}

			$this->addHeader('Content-length: ' . strlen($data));

			curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $this->method);
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
		}

		// Add headers
		if (count($this->headers)) {
			curl_setopt($handle, CURLOPT_HTTPHEADER, $this->headers);
		}

		// Add custom curl options
		if (count($this->options)) {
			foreach ($this->options as $option => $value) {
				curl_setopt($handle, $option, $value);
			}
		}

		$output = curl_exec($handle);

		$response->setInfo(curl_getinfo($handle));
		$response->setResponse($output, $this->returnHeader);

		curl_close($handle);

		unset($output);
		unset($handle);

		return $response;
	}

}