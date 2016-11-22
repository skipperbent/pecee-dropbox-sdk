# pecee/dropbox-sdk
Small, lightweight PHP-SDK for v2 of the Dropbox api.

## Installing the SDK

Add the latest version of the pecee/dropbox-sdk by running the following command:

```composer require pecee/dropbox-sdk```

### Note

This project is created as part of another project. All the features we needed were `upload`, `delete` and `download`.
This SDK is extremely basic and might not be suitable for everyone.

**If you really want to see a feature implemented; feel free to open a new issue**

## Usage

#### Uploading file

```php
$service = new Pecee\Http\Dropbox\Dropbox($accessToken);

$imageContents = file_get_contents('image.jpg');

$response = $service->upload($imageContents, '/destination/image.jpg');
```

#### Downloading file

```php
$service = new Pecee\Http\Dropbox\Dropbox($accessToken);
$this->service->download('/destination/image.jpg');
```

#### Deleting file

```php
$service = new Pecee\Http\Dropbox\Dropbox($accessToken);
$response = $service->delete('/destination/image.jpg');
```

## The MIT License (MIT)

Copyright (c) 2016 pecee

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
