<?php
set_time_limit(0);
//ini_set('max_execution_time', 300); //300 seconds = 5 minutes
/*
 * Converts CSV to JSON
 * Example uses Google Spreadsheet CSV feed
 * csvToArray function I think I found on php.net
 */
header('Content-type: application/json');
// Set your CSV feed
//$feed = 'https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&key=0Akse3y5kCOR8dEh6cWRYWDVlWmN0TEdfRkZ3dkkzdGc&single=true&gid=0&output=csv';
$feed = "ACTIVE.txt";
// Arrays we'll use later
$keys = array();
$newArray = array();
// Function to convert CSV into associative array
function csvToArray($file, $delimiter) { 
  if (($handle = fopen($file, 'r')) !== FALSE) { 
    $i = 0; 
    while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) { 
      for ($j = 0; $j < count($lineArray); $j++) { 
        $arr[$i][$j] = $lineArray[$j]; 
      } 
      $i++; 
    } 
    fclose($handle); 
  } 
  return $arr; 
} 
// Do it
$data = csvToArray($feed, "\t");
// Set number of elements (minus 1 because we shift off the first row)
$count = count($data) - 1;
  
//Use first row for names  
$labels = array_shift($data);  
foreach ($labels as $label) {
  $keys[] = $label;
}
// Add Ids, just in case we want them later
$keys[] = 'id';
for ($i = 0; $i < $count; $i++) {
  $data[$i][] = $i;
}
  
// Bring it all together
for ($j = 0; $j < $count; $j++) {
  $d = array_combine($keys, $data[$j]);
  $newArray[$j] = $d;
}
// Print it out as JSON
//echo json_encode($newArray);
//We now have ACTIVE in an array

print_r($newArray);
echo count($newArray);
//print_r($newArray[2151]['quantity']);
if($newArray[2151]['quantity']== ""){
	print("true");
}
//now we will pull in the shopify catalog, convert it to an array to make checks
//
//for($k=5; $k<10; $k++){

$baseUrl = 'https://key:password@domain.com/admin/';

$getUrl = $baseUrl.'products.json?limit=250&page=10';

$ch = curl_init();  //note product ID in url
curl_setopt($ch, CURLOPT_URL, $getUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");  //specify the PUT verb for update
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //set return as string true
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'authorization: Basic MmZkOTY0Mjcw=',
'accept-language: en-US,en;q=0.8',
'user-agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.112 Safari/537.36'
)); //set the header as JSON
$server_output = curl_exec ($ch); //execute and store server output


//print_r(curl_getinfo($ch));
curl_close ($ch); //close the connection
//echo $server_output;

//print_r(json_decode($server_output));
$decoded = json_decode($server_output);
print_r($decoded->products[0]->variants[0]->id);
echo "<br>";
print_r($decoded->products[0]->variants[0]->sku);
echo "<br>";
print_r($decoded->products[0]->variants[0]->inventory_quantity);
echo "<br>";


$myproducts = $decoded->products;
echo count($myproducts);
$newShopifyArray = array();

//print_r($decoded->products[0]->variants[0]->id);


//$decoded->products[0]->variants[0]->id = $newShopifyArray[0]["id"];
print_r($myproducts);

$key = array_search("1179", array_column($newArray, 'seller-sku'), true);
echo "i am" . $key . "i am";
if(empty($newArray[$key]['quantity'])){
  echo "true";
}elseif (!empty($newArray[$key]['quantity'])) {
 echo "false";
}

for ($i = 0, $j=0; $i < count($myproducts); $i++){
  $error;
  $lookupSku = $decoded->products[$i]->variants[0]->sku;
  $key = array_search($lookupSku, array_column($newArray, 'seller-sku'), true);
  if(!array_key_exists($key, $newArray)){
      echo $lookupSku . " doesnt exist in ACTIVE, its closed out or deleted!, log it to cleanup SHOPIFY and to do SEO redirects\n" ;
      $newShopifyArray[$j]["id"] = $decoded->products[$i]->id;
      $newShopifyArray[$j]["published"] = "false";
      //$newShopifyArray[$j]["inventory_quantity"] = 0;
      $pv = 'product';
      $pvs = 'products/';
      putIt($pvs, $pv, $j, $newShopifyArray);
      $newShopifyArray[$j]["id"] = $decoded->products[$i]->variants[0]->id;
       $newShopifyArray[$j]["inventory_quantity"] = "0";
       $pv = 'variant';
      $pvs = 'variants/';
      putIt($pvs, $pv, $j, $newShopifyArray);
    $j++;
  }
	elseif($decoded->products[$i]->variants[0]->inventory_quantity == $newArray[$key]['quantity'] && is_numeric($newArray[$key]['quantity'])){

	// $newShopifyArray[$i]["id"] = $decoded->products[$i]->variants[0]->id;
	// $newShopifyArray[$i]["sku"] = $decoded->products[$i]->variants[0]->sku;
	// $newShopifyArray[$i]["quantity"] = $decoded->products[$i]->variants[0]->inventory_quantity;
		echo $lookupSku . " is equal\n";
    
	}elseif (!is_numeric($newArray[$key]['quantity'])) {
    echo $lookupSku . "FBA product - change shopify to invisible qty 0. test visibility and quantity in shopify \n";
      $newShopifyArray[$j]["id"] = $decoded->products[$i]->id;
      $newShopifyArray[$j]["published"] = "false";
      //$newShopifyArray[$j]["inventory_quantity"] = 0;
    $pv = 'product';
    $pvs = 'products/';
    putIt($pvs, $pv, $j, $newShopifyArray);
    $newShopifyArray[$j]["id"] = $decoded->products[$i]->variants[0]->id;
       $newShopifyArray[$j]["inventory_quantity"] = "0";
       $pv = 'variant';
      $pvs = 'variants/';
      putIt($pvs, $pv, $j, $newShopifyArray);
    $j++;


      }elseif ($decoded->products[$i]->variants[0]->inventory_quantity != $newArray[$key]['quantity'] && is_numeric($newArray[$key]['quantity'])) {
    $newShopifyArray[$j]["id"] = $decoded->products[$i]->variants[0]->id;
   $newShopifyArray[$j]["sku"] = $decoded->products[$i]->variants[0]->sku;
   $newShopifyArray[$j]["inventory_quantity"] = $newArray[$key]['quantity'];
     $pv = 'variant';
   $pvs = 'variants/';
    putIt($pvs, $pv, $j, $newShopifyArray);
    $newShopifyArray[$j]["id"] = $decoded->products[$i]->id;
      $newShopifyArray[$j]["published"] = "true";
      //$newShopifyArray[$j]["inventory_quantity"] = 0;
    $pv = 'product';
    $pvs = 'products/';
    putIt($pvs, $pv, $j, $newShopifyArray);
    $j++;
  }
}

function putIt($pvs, $pv, $j, $newShopifyArray){

//print_r($newShopifyArray);

//$encodedJson = json_encode(array('variant'=>$newShopifyArray[0]));
//print_r($encodedJson);

//push the json to shopify
$baseUrl = 'https://key:password@domain.com/admin/';


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
'authorization: Basic MmZkOTY0MjcwMDBkODEwNTQ=',
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
//}//for loop to paginate
exit;


//really dont need to do this, we are truncating shopify, adding and using it in put
for ($i = 0; $i < count($newArray); $i++){
$myarray[$i]["seller-sku"] = $newArray[$i]["seller-sku"];
$myarray[$i]["quantity"] = $newArray[$i]["quantity"];
}
//print_r($myarray);
//echo $myarray[0]["afn-fulfillable-quantity"];



$i = 0;
foreach($myarray as $value){
foreach($value as $key => $value2){
if ($value2 == ""){
$value2 = $myarray2[$i]["afn-fulfillable-quantity"];
  echo "\$myarray[$key] => $value2\n";
}
$i++;
}
}
print_r($myarray);

//array_pad($myarray2, 2200, "foo");
//print_r($myarray2);


//$myarray3 = array_replace($myarray, $myarray2);
//print_r($myarray3);



?>