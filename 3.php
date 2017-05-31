<?php 
header('Content-Type:text/html; charset=utf-8');
$data = unserialize('a:23:{s:8:"discount";s:4:"0.00";s:12:"payment_type";s:1:"1";s:7:"subject";s:15:"测试的商品";s:8:"trade_no";s:28:"2017051221001004110258079100";s:11:"buyer_email";s:17:"1217103565@qq.com";s:10:"gmt_create";s:19:"2017-05-12 11:49:48";s:11:"notify_type";s:17:"trade_status_sync";s:8:"quantity";s:1:"1";s:12:"out_trade_no";s:15:"0512114942-5322";s:9:"seller_id";s:16:"2088621976521513";s:11:"notify_time";s:19:"2017-05-12 11:49:49";s:4:"body";s:30:"该测试商品的详细描述";s:12:"trade_status";s:13:"TRADE_SUCCESS";s:19:"is_total_fee_adjust";s:1:"N";s:9:"total_fee";s:4:"0.01";s:11:"gmt_payment";s:19:"2017-05-12 11:49:49";s:12:"seller_email";s:17:"2736637078@qq.com";s:5:"price";s:4:"0.01";s:8:"buyer_id";s:16:"2088802716594110";s:9:"notify_id";s:34:"1d198ba33f256d05cb5811274052364gum";s:10:"use_coupon";s:1:"N";s:9:"sign_type";s:3:"RSA";s:4:"sign";s:172:"Jp+LhO0q+KPKBD/s2XErKN0ubJCAB9UCugpwfxJc0RsqsaEsrMeYB6d2R2ZTmMwdmkP1GltjQNcdCNW98Rit41fHXUSTvMXfP0QyCZC/L5wl4ehx1KXxBd5KzV5b/+cK1t9p0hs9Y1EaUNSEmy8jDu0uuzO8gLUu/js3kcQut7M=";}');
//除去待签名参数数组中的空值和签名参数
//$para_filter = paraFilter($data);

//对待签名参数数组排序
//$para_sort = argSort($data);

//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
//$prestr = createLinkstring($data);
//var_dump($data);exit;
$dd=getSignVeryfy($data,$data['sign']);
var_dump($dd);
/**
 * 除去数组中的空值和签名参数
 * @param $para 签名参数组
 * return 去掉空值与签名参数后的新签名参数组
 */
function paraFilter($para) {
	$para_filter = array();
	while (list ($key, $val) = each ($para)) {
		if($key == "sign" || $key == "sign_type" || $val == "")continue;
		else	$para_filter[$key] = $para[$key];
	}
	return $para_filter;
}
/**
 * 对数组排序
 * @param $para 排序前的数组
 * return 排序后的数组
 */
function argSort($para) {
	ksort($para);
	reset($para);
	return $para;
}
/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para 需要拼接的数组
 * return 拼接完成以后的字符串
 */
function createLinkstring($para) {
	$arg = "";
	while (list($key, $val) = each($para)) {
		$arg .= $key . "=" . $val . "&";
	}
	//去掉最后一个&字符
	$arg = substr($arg, 0, count($arg) - 2);

	//如果存在转义字符，那么去掉转义
	if (get_magic_quotes_gpc()) {$arg = stripslashes($arg);}

	return $arg;
}

/**
 * 获取返回时的签名验证结果
 * @param $para_temp 通知返回来的参数数组
 * @param $sign 返回的签名结果
 * @return 签名验证结果
 */
function getSignVeryfy($para_temp, $sign) {
	//除去待签名参数数组中的空值和签名参数
	$para_filter = paraFilter($para_temp);

	//对待签名参数数组排序
	$para_sort = argSort($para_filter);

	//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	$prestr = createLinkstring($para_sort);

	$isSgin = false;
	switch (strtoupper(trim('RSA'))) {
		case "RSA":
			$isSgin = rsaVerify($prestr, trim(getcwd().'/alipay_app/key/alipay_public_key.pem'), $sign);
			break;
		default:
			$isSgin = false;
	}

	return $isSgin;
}
/**
 * RSA验签
 * @param $data 待签名数据
 * @param $ali_public_key_path 支付宝的公钥文件路径
 * @param $sign 要校对的的签名结果
 * return 验证结果
 */
function rsaVerify($data, $ali_public_key_path, $sign) {
	$pubKey = file_get_contents($ali_public_key_path);
	$res = openssl_get_publickey($pubKey);
	$result = (bool) openssl_verify($data, base64_decode($sign), $res);
	openssl_free_key($res);
	return $result;
}

?>