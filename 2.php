<?php
echo 'testhdw';
echo 'hahahahaha';
if(false){
	echo '1111';
}else{
	echo '2222';
}
function ordersn(){
	$orderSn = 'QY' . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $orderSn;
}