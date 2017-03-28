<?php

namespace Weblebby\Payments;

class PaymentParent
{
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
}