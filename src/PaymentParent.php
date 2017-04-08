<?php

namespace Weblebby\Payments;

class PaymentParent
{
	/**
	 * The config.
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * The post data.
	 *
	 * @var array
	 */
	protected $post = [];

	/**
	 * Check same trans id.
	 *
	 * @param mixed $trans_ids
	 * @return boolean 
	 */
	protected function checkTransParent($trans_ids = [], $post)
	{
		if ( isset($post['trans_id']) !== true ) {
			return false;
		}

		if ( is_array($trans_ids) !== true ) {
			$trans_ids = [$trans_ids];
		}

		return in_array($post['trans_id'], $trans_ids) !== true;
	}

	/**
	 * Get request fields.
	 *
	 * @return boolean|array
	 */
	protected function fieldsParent($fields)
	{
		$result = [];

		if ( is_array($fields) !== true ) {
			return false;
		}

		foreach ($fields as $key => $value) {
			$result[$value] = isset($_POST[$value]) ? $_POST[$value] : null;
		}

		return $result;
	}

	/**
	 * Get client ip address.
	 */
	protected function clientIp()
	{
	    if ( getenv('HTTP_CLIENT_IP') ) $ip = getenv('HTTP_CLIENT_IP');
	    else if ( getenv('HTTP_X_FORWARDED_FOR') ) $ip = getenv('HTTP_X_FORWARDED_FOR');
	    else if ( getenv('HTTP_X_FORWARDED') ) $ip = getenv('HTTP_X_FORWARDED');
	    else if ( getenv('HTTP_FORWARDED_FOR') ) $ip = getenv('HTTP_FORWARDED_FOR');
	    else if ( getenv('HTTP_FORWARDED') ) $ip = getenv('HTTP_FORWARDED');
	    else if ( getenv('REMOTE_ADDR') ) $ip = getenv('REMOTE_ADDR');
	    else $ip = 'UNKNOWN';

	    return $ip;
	}
}