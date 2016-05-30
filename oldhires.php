<?php
set_time_limit ( 0 );
$myASIN = array(
"B01ETVQA6I"
);
$fp = fopen('data.txt', 'w');
$curl = curl_init();
for($i=0; $i< count($myASIN); $i++){
$url = 'http://www.amazon.com/dp/' . $myASIN[$i];

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );   
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$resp = curl_exec($curl);

//echo print_r($resp);
//echo "\n";
$image = amazon_scrape($resp);
$title = amazon_scrape2($resp);
$price = amazon_scrape_price($resp);
$sku = amazon_scrape_sku($resp);
$itemNum = amazon_scrape_itemNum($sku[0]);
$bullet = amazon_scrape_bullet($resp);
$desc = amazon_scrape_desc($resp);
$desc2 = amazon_scrape_desc2(urldecode($desc[0]));
$bulletStrip = amazon_scrape_bulletStrip($bullet[1]);

fwrite($fp, $myASIN[$i]);
fwrite($fp, ",");
fwrite($fp, $image[0]);
fwrite($fp, "\n");
echo $image[0];
echo "\n";
echo "<br>";
echo htmlspecialchars_decode(trim($title[1]));
$cleantitle = htmlspecialchars_decode(trim($title[1]));
echo "\n";
echo "<br>";
echo trim($price[1]);
$cleanPrice = trim($price[1]);
echo "\n";
echo "<br>";
echo $myASIN[$i];
echo "\n";
echo "<br>";
echo trim(strip_tags($sku[1]));
$cleanSku = trim(strip_tags($sku[1]));
echo "\n";
echo "<br>";
//echo strip_tags($itemNum[0]);
//echo "\n";
//echo "<br>";
//echo $bullet[1];
//echo "\n";
//echo "<br>";
print_r($bulletStrip[0]);
echo "\n";
echo "<br>";
//echo $desc[0];
//echo "<br>";
//echo urldecode($desc[0]);
echo trim(strip_tags($desc2[1]));
$cleanDesc = trim(strip_tags($desc2[1]));
echo "\n";
echo "<br>";
echo "\n";
echo "<br>";


$newShopifyArray = array('title' => $cleantitle,
'body_html' => $bulletStrip[0] . "<p>" .$cleanDesc . "</p>",
'image' => $image[0],
'vendor' => 'Banberry Designs',
'price' => $cleanPrice,
'sku' => $cleanSku,
'barcode' => $myASIN[$i],
'inventory_policy' => 'shopify',
'inventory_quantity' => '10',
'weight' => '1'
 );

print_r($newShopifyArray);


}
function amazon_scrape($html) {
	preg_match('#data-old-hires="(.*?)"#s', $html, $price);
	return $price;
}

function amazon_scrape2($html){
	preg_match('#<span id="productTitle" class="a-size-large">(.*?)</span>#s', $html, $price);
return $price;
}
function amazon_scrape_price($html){
	preg_match('#<span id="priceblock_ourprice" class="a-size-medium a-color-price">(.*?)</span>#s', $html, $price);
return $price;
}
function amazon_scrape_sku($html){
	preg_match('#Item model number(.*?)</td>#s', $html, $price);
return $price;
}
function amazon_scrape_bullet($html){
	preg_match('#<ul class="a-vertical a-spacing-none">(.*?)</ul>#s', $html, $price);
return $price;
}
function amazon_scrape_desc($html){
	preg_match('#var iframeContent = "(.*?)"#s', $html, $price);
return $price;
}
function amazon_scrape_desc2($html){
	preg_match('# <div class="productDescriptionWrapper">(.*?)</div>#s', $html, $price);
return $price;
}
function amazon_scrape_itemNum($html){
	preg_match('#<td class="a-size-base">(.*?)#', $html, $price);
return $price;
}
function amazon_scrape_bulletStrip($html){
	preg_match_all('#<li><span class="a-list-item">(.*?)</span></li>#s', $html, $price);
return $price;
}


//post to shopify


function putIt($pvs, $pv, $j, $newShopifyArray){

//print_r($newShopifyArray);

//$encodedJson = json_encode(array('variant'=>$newShopifyArray[0]));
//print_r($encodedJson);

//push the json to shopify
$baseUrl = 'https://2fd96427000d810540f44b461722a816:0a1de82a15f4f92c28f1f1497531b4b9@relevant-gifts.myshopify.com/admin/';


/*$variant =
array("inventory_quantity" => 0
);*/

$putUrl = $baseUrl. $pvs . $newShopifyArray[$j]["id"] . '.json';

$data_string = json_encode(array($pv=>$newShopifyArray[$j])); //json encode the product array

$ch = curl_init();  //note product ID in url
curl_setopt($ch, CURLOPT_URL, $putUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");  //specify the PUT verb for update
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  //add the data string for the request
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //set return as string true
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'authorization: Basic MmZkOTY0MjcwMDBkODEwNTQwZjQ0YjQ2MTcyMmE4MTY6MGExZGU4MmExNWY0ZjkyYzI4ZjFmMTQ5NzUzMWI0Yjk=',
'Content-Type: application/json',
'accept: application/json',
'accept-encoding: gzip, deflate',
'accept-language: en-US,en;q=0.8',
'user-agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36',
'Content-Length: ' . strlen($data_string))
); //set the header as JSON
$server_output = curl_exec ($ch); //execute and store server output


echo $data_string;
print_r(curl_getinfo($ch));
curl_close ($ch); //close the connection
echo $server_output;

//We now have the shopify catalog
sleep(1);

}





curl_close($curl);
//data-a-dynamic-image='{"http://ecx.images-amazon.com/images/I/61ddIHlEU0L._SY355_.jpg":[296,355],"h
//data-old-hires="http://ecx.images-amazon.com/images/I/61ddIHlEU0L._SL1000_.jpg"





?>

