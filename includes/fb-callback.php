<?php require_once("init.php");

// app id and secret value's are found on the dashboard
$fb = new Facebook\Facebook([
	'app_id' => '236757293332894',
	'app_secret' => 'e10529583413ed7c57274dd271d2a0e5',
	'default_graph_version' => 'v2.2',
]);

$helper = $fb->getRedirectLoginHelper();

try {
	$accessToken = $helper->getAccessToken();
} catch (Facebook\Exceptions\FacebookResponseException $e) {
	echo 'Graph returned an error: ' . $e->getMessage();
	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
	exit;
}

if (! isset($accessToken)) {
	if ($helper->getError()) {
		header('HTTP/1.0 401 Unauthorized');
		echo "Error: " . $helper->getError() . "\n";
		echo "Error Code: " . $helper->getErrorCode() . "\n";
		echo "Error Reason: " . $helper->getErrorReason() . "\n";
		echo "Error Description: " . $helper->getErrorDescription() . "\n";
	} else {
		header('HTTP/1.0 400 Bad Request');
		echo 'Bad request';
	}
	exit;
}

$oAuth2Client = $fb->getOAuth2Client();
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
$tokenMetadata->validateAppId('236757293332894');
$tokenMetadata->validateExpiration();

if (!$accessToken->isLongLived()) {
	try {
		$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
		exit;
	}
}
$token = (string) $accessToken;

try {	
	$response = $fb->get('/me?fields=id,name,picture,email', $token);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
	echo 'Graph returned an error: ' . $e->getMessage();
	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
	exit;
}
$profile = $response->getGraphNode();
$result = DB::query("SELECT username FROM users WHERE fb_id = ?; ", array($profile["id"]));
if ($row = $result->fetch()) {
	// user is already authenticated
	$_SESSION['username'] = $row["username"];
} else {
	// user needs to register for the first time
	$_SESSION['fb_access_token'] = $token;
	$_SESSION['fb_id'] = $profile["id"];
	$_SESSION['fb_name'] = $profile["name"];
}
redirect(); // redirect the user back to the index.php page



