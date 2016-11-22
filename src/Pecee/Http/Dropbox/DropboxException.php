<?php
namespace Pecee\Http\Dropbox;

use Pecee\Http\HttpException;
use Pecee\Http\HttpResponse;

class DropboxException extends HttpException
{

	protected $errors = [
		'not_found' => 'File not found.',
	];

	public function __construct($message, $code = 0, HttpResponse $httpResponse = null)
	{
		parent::__construct($message, $code);

		$this->httpResponse = $httpResponse;

		$object = json_decode($httpResponse->getResponse(), true);

		if (is_array($object) && isset($object['error_summary'])) {

			$error = null;

			foreach ($this->errors as $k => $e) {
				if (strstr($object['error_summary'], $k) !== false) {
					$error = $e;
					break;
				}
			}

			if ($error !== null) {
				$this->message .= '. ' . $error;
			} else {
				$this->message .= ' (' . $object['error_summary'] . ')';
			}
		}
	}

}