<?php
namespace Pecee\Http;

class HttpResponse
{
	protected $response;
	protected $info = [];
	protected $headers = [];

	public function parseHeader($handle, $header)
	{
		$details = explode(':', $header, 2);

		if (count($details) == 2) {
			$key = trim($details[0]);
			$value = trim($details[1]);

			$this->headers[$key] = $value;
		}

		return strlen($header);
	}

	public function getInfo()
	{
		return $this->info;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function getUrl()
	{
		return isset($this->info['url']) ? $this->info['url'] : null;
	}

	public function getContentType()
	{
		return isset($this->info['content_type']) ? $this->info['content_type'] : null;
	}

	public function getRequestSize()
	{
		return isset($this->info['request_size']) ? $this->info['request_size'] : null;
	}

	public function getHeaderSize()
	{
		return isset($this->info['header_size']) ? $this->info['header_size'] : null;
	}

	public function getStatusCode()
	{
		return isset($this->info['http_code']) ? $this->info['http_code'] : null;
	}

	public function getTotalTime()
	{
		return isset($this->info['total_time']) ? $this->info['total_time'] : null;
	}

	public function setInfo(array $info)
	{
		$this->info = $info;

		return $this;
	}

	public function getHeader($key, $default = null)
	{
		foreach($this->headers as $k => $value) {
			if(strtolower($key) === $k) {
				return $value;
			}
		}

		return $default;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function setResponse($response, $removeHeaders = false)
	{
		if ($removeHeaders) {
			$this->response = substr($response, $this->getHeaderSize());

			return $this;
		}

		$this->response = $response;

		return $this;
	}

}