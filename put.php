<?php
$baseUrl = 'https://apikey:password@domain.myshopify.com/admin/';


$variant =
array("inventory_quantity" => 0
);

$putUrl = $baseUrl.'variants/19175889219.json';

$data_string = json_encode(array('variant'=>$variant)); //json encode the product array

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


?>  