<?php

namespace Weblebby\Payments;

class BatigamePayment extends PaymentParent
{
	/**
	 * HtmlForm Config
	 *
	 * @var object
	 */
	public $htmlForm;

	/**
	 * Load the class
	 *
	 * @param array $config
	 * @return void
	 */
	public function __construct(array $config)
	{
		$this->config['id'] = $config['id'];
		$this->config['secret'] = $config['secret'];
		$this->config['api_url'] = $config['api_url'];

		$this->loadHtmlFormDefaultConfig();
	}

	/**
	 * Open the html form.
	 *
	 * @return string
	 */
	public function openHtmlForm($url = null)
	{
		if ( is_null($url) ) {
			$url = $this->config['api_url'];
		}

		$inputs = [
			'username' => 'oyuncu',
			'success_url' => 'odemeolduurl',
			'error_url' => 'odemeolmadiurl',
			'vip_name' => 'vipname',
			'report_email' => 'raporemail',
			'only_email' => 'onlyemail',
			'post_url' => 'posturl'
		];

		$inputHtml = [];

		foreach ( get_object_vars($this->htmlForm) as $key => $input ) {
			if ( empty($input) === true ) {
				continue;
			}

			$inputHtml[] = '<input type="hidden" name="' . $inputs[$key] . '" value="' . $input . '">';
		}

		return '<form action="' . $url . '" method="post" autocomplete="off">
	' . ( implode("\n", $inputHtml) ) . '
	<input type="hidden" name="batihostid" value="' . $this->config['id'] . '">';
	}

	/**
	 * Close the html form.
	 *
	 * @return string
	 */
	public function closeHtmlForm()
	{
		return '</form>';
	}

	/**
	 * Handle received request.
	 *
	 * @return array
	 */
	public function handle()
	{
		if ( isset($_POST['transid'], $_POST['user'], $_POST['credit'], $_POST['guvenlik']) !== true ) {
			throw new PaymentException("Gelen post eksik. [transid, user, credit, guvenlik]", 1);
		}

		$post = [
			'trans_id' => $_POST['transid'],
			'username' => $_POST['user'],
			'credit' => $_POST['credit'],
			'secret' => $_POST['guvenlik']
		];

		if ( $post['secret'] !== $this->config['secret'] ) {
			throw new PaymentException("Güvenlik kodu uyuşmuyor.", 1);
		}

		$this->post = $post;

		return $post;
	}

	/**
	 * Check same trans id.
	 *
	 * @param mixed $trans_ids
	 * @return boolean 
	 */
	public function checkTrans($trans_ids = [])
	{
		return $this->checkTransParent($trans_ids, $this->post);
	}

	/**
	 * Get request fields.
	 *
	 * @return array|boolean
	 */
	public function fields()
	{
		if ( isset($this->fields) !== true ) {
			return false;
		}
		
		return $this->fieldsParent($this->fields);
	}

	/**
	 * Load default config of html form.
	 *
	 * @return void
	 */
	protected function loadHtmlFormDefaultConfig()
	{
		$this->htmlForm = new \stdClass();

		$this->htmlForm->username = null;
		$this->htmlForm->success_url = null;
		$this->htmlForm->error_url = null;
		$this->htmlForm->vip_name = null;
		$this->htmlForm->report_email = null;
		$this->htmlForm->only_email = null;
		$this->htmlForm->post_url = null;
	}
}