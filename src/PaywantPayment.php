<?php

namespace Weblebby\Payments;

class PaywantPayment extends PaymentParent
{
	/**
	 * Response data.
	 *
	 * @var array $response
	 */
	public $response;

	/**
	 * The validation rules.
	 *
	 * @param string $str
	 * @return array
	 */
	public function validation($str = null) {
		if ( in_array($str, ['rules', 'attributes']) !== true ) {
			$str = 'rules';
		}

		$rules = [
			'username' => 'required|max:255|exists:users',
			'credit' => 'required|numeric'
		];

		$attributes = [
			'username' => __('Kullanıcı Adınız'),
			'credit' => __('Kredi')
		];

		return $$str;
	}

	/**
	 * Load the class
	 *
	 * @param array $config
	 * @param array $post
	 * @return void
	 */
	public function __construct(array $config, array $post = [])
	{
		$this->config['key'] = $config['key'];
		$this->config['secret'] = $config['secret'];
		$this->config['api_url'] = $config['api_url'];

		$this->sendPayload($post);
	}

	/**
	 * Handle received request.
	 *
	 * @return array
	 */
	public function handle()
	{
		if ( isset($_POST['SiparisID'], $_POST['ExtraData'], $_POST['UserID'], $_POST['ReturnData'], $_POST['Status'], $_POST['OdemeKanali'], $_POST['OdemeTutari'], $_POST['NetKazanc'], $_POST['Hash']) !== true ) {
			throw new PaymentException("Gelen post eksik.", 1);
		}

		$this->post = [
			'trans_id' => $_POST['SiparisID'],
			'extra_data' => $_POST['ExtraData'],
			'username' => $_POST['UserID'],
			'return_data' => $_POST['ReturnData'],
			'status' => $_POST['Status'],
			'payment_channel' => $_POST['OdemeKanali'],
			'payment_price' => $_POST['OdemeTutari'],
			'credit' => $_POST['OdemeTutari'],
			'gain' => $_POST['NetKazanc']
		];

		$checkHash = base64_encode(hash_hmac('sha256', implode('|', [
			$this->post['trans_id'],
			$this->post['extra_data'],
			$this->post['username'],
			$this->post['return_data'],
			$this->post['status'],
			$this->post['payment_channel'],
			$this->post['payment_price'],
			$this->post['gain']
		]) . $this->config['key'], $this->config['secret'], true));

		if ( $checkHash !== $_POST['Hash'] ) {
			throw new PaymentException("Güvenlik kodu hatalı.", 1);
		}

		if ( $this->post['status'] != 100 ) {
			return $post;
		}

		return $post;
	}

	/**
	 * Send payload to Paywant Server
	 *
	 * @return array
	 */
	protected function sendPayload($post)
	{
		if ( isset($post['username'], $post['email'], $post['user_id'], $post['credit']) !== true ) {
			return false;
		}

		$generateHash = base64_encode(hash_hmac('sha256', $post['username'] . '|' . $post['email'] . '|' . $post['user_id'] . $this->config['key'], $this->config['secret'], true));
		
		$productData = [
			'name' =>  $post['credit'] . ' Türk Lirası',
			'amount' => $post['credit'] * 100,
			'extraData' => $post['credit'],
			'paymentChannel' => '1,2,3',
			'commissionType' => 1
		];

		$postData = [
			'apiKey' => $this->config['key'],
			'hash' => $generateHash,
			'returnData'=> $post['username'],
			'userEmail' => $post['email'],
			'userIPAddress' => $this->clientIp(),
			'userID' => $post['user_id'],
			'proApi' => true,
			'productData' => $productData
		];
		
		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => $this->config['api_url'],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>  http_build_query($postData),
		]);
		
		$response = curl_exec($curl);
		$error = curl_error($curl);

		if ( $error ) {
			throw new PaymentException("Bir hata oluştu, lütfen admine işlem saatiyle birlikte haber verin.", true);
		}

		$responseJson = json_decode($response, false);
		$status = isset($responseJson->Status) ? (int) $responseJson->Status : null;

		curl_close($curl);

		$this->response = $responseJson;

		return $responseJson;
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
}