<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/header.php';
require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';
if(isset($user) && $user->isLoggedIn()){
}
?>
<?php

/**
 * Verify transaction is authentic
 *
 * @param array $data Post data from Paypal
 * @return bool True if the transaction is verified by PayPal
 * @throws Exception
 */
function verifyTransaction($data) {
	global $paypalUrl;

	$req = 'cmd=_notify-validate';
	foreach ($data as $key => $value) {
		$value = urlencode(stripslashes($value));
		$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
		$req .= "&$key=$value";
	}

	$ch = curl_init($paypalUrl);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSLVERSION, 6);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
	$res = curl_exec($ch);

	if (!$res) {
		$errno = curl_errno($ch);
		$errstr = curl_error($ch);
		curl_close($ch);
		throw new Exception("cURL error: [$errno] $errstr");
	}

	$info = curl_getinfo($ch);

	// Check the http response
	$httpCode = $info['http_code'];
	if ($httpCode != 200) {
		throw new Exception("PayPal responded with http code $httpCode");
	}

	curl_close($ch);

	return $res === 'VERIFIED';
}

/**
 * Check we've not already processed a transaction
 *
 * @param string $txnid Transaction ID
 * @return bool True if the transaction ID has not been seen before, false if already processed
 */
function checkTxnid($txnid) {
	global $db;

	$txnid = $db->real_escape_string($txnid);
	$results = $db->query('SELECT * FROM `payments` WHERE txnid = \'' . $txnid . '\'');

	return ! $results->num_rows;
}

/**
 * Add payment to database
 *
 * @param array $data Payment data
 * @return int|bool ID of new payment or false if failed
 */
function addPayment($data) {
	global $db;

	if (is_array($data)) {
        $stmt = $pdo->prepare('INSERT INTO `payments` (id, item_name, item_number  txnid, payment_amount, payment_status, itemid, createdtime, user_id) VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bindParam(1, $data['item_name'], PDO::PARAM_STR);
        $stmt->bindParam(2, $data['item_number'], PDO::PARAM_STR);
        $stmt->bindParam(3, $data['txn_id'], PDO::PARAM_STR);
        $stmt->bindParam(4, $data['payment_amount'], PDO::PARAM_STR);
        $stmt->bindParam(5, $data['payment_status'], PDO::PARAM_STR);
        $stmt->bindParam(6, $data['item_number'], PDO::PARAM_STR);
        $stmt->bindParam(7, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindParam(8, $data['buyer_id'], PDO::PARAM_STR);
        $stmt->execute();

		return $db->insert_id;
}

	return false;
}

?>

    <!-- footers -->
<?php require_once $abs_us_root.$us_url_root.'users/includes/page_footer.php'; // the final html footer copyright row + the external js calls ?>

    <!-- Place any per-page javascript here -->


<?php require_once $abs_us_root.$us_url_root.'users/includes/html_footer.php'; // currently just the closing /body and /html ?>