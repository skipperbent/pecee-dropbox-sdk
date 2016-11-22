<?php

class DropboxTest extends PHPUnit_Framework_TestCase
{
	protected $service;
	protected $accessToken = '!!!INSERT-ACCESS-TOKEN-HERE!!!';
	protected $imageUploadPath = '/dscuz/debug/test.jpg';

	public function __construct()
	{
		$this->service = new Pecee\Http\Dropbox\Dropbox($this->accessToken);
	}

	public function testUpload()
	{
		$image = file_get_contents('test.jpg');
		$response = $this->service->upload($image, $this->imageUploadPath);

		$this->assertTrue(is_array($response));
		$this->assertEquals($this->imageUploadPath, $response['path_lower']);
	}

	public function testDownload()
	{
		$response = $this->service->download($this->imageUploadPath);

		$this->assertArrayHasKey('image', $response);
		$this->assertNotNull($response['image']);
	}

	public function testDelete()
	{
		$response = $this->service->delete($this->imageUploadPath);

		$this->assertTrue(is_array($response));
		$this->assertEquals($this->imageUploadPath, $response['path_lower']);
	}

}