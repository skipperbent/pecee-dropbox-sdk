<?php
namespace Pecee\Dropbox;

use Pecee\TestCase;

class DropboxTest extends TestCase
{
    protected $imageUploadPath = '/test/test.jpg';

	public function testUpload()
	{
		$image = file_get_contents($this->assetsPath . '/test.jpg');
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