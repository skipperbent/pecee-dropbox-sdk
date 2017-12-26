<?php

namespace Pecee;

use Pecee\Http\Dropbox\Dropbox;

class TestCase extends \PHPUnit\Framework\TestCase
{

    protected $accessToken;
    protected $service;
    protected $assetsPath;

    protected function setUp()
    {
        parent::setUp();

        global $accessToken;
        $this->accessToken = $accessToken;
        $this->service = new Dropbox($this->accessToken);
        $this->assetsPath = __DIR__ . '/assets';

        $this->service->getHttpRequest()->setOptions([
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
    }

}