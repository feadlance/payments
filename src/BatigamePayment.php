<?php

namespace Weblebby\Payments;

class BatigamePayment
{
	/**
	 * Batıhost Config.
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * Batıhost Post Data.
	 *
	 * @var array
	 */
	protected $post = [];

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

		$this->loadHtmlFormDefaultConfig();
	}

	/**
	 * Open the html form.
	 *
	 * @return string
	 */
	public function openHtmlForm()
	{
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

		return '<form action="http://batigame.com/vipgateway/viprec.php" method="post" autocomplete="off">
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
		if ( isset($this->post['trans_id']) !== true ) {
			return false;
		}

		if ( is_array($trans_ids) !== true ) {
			$trans_ids = [$trans_ids];
		}

		return in_array($this->post['trans_id'], $trans_ids) !== true;
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