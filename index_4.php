<?php
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
//print_r($newArray[0]);
//echo count($newArray);
for ($i = 0; $i < count($newArray); $i++){
$myarray[$i]["seller-sku"] = $newArray[$i]["seller-sku"];
$myarray[$i]["quantity"] = $newArray[$i]["quantity"];
}
//print_r($myarray);
//echo $myarray[0]["afn-fulfillable-quantity"];



header('Content-type: application/json');
// Set your CSV feed
//$feed = 'https://docs.google.com/spreadsheet/pub?hl=en_US&hl=en_US&key=0Akse3y5kCOR8dEh6cWRYWDVlWmN0TEdfRkZ3dkkzdGc&single=true&gid=0&output=csv';
$feed = "MFI.txt";
// Arrays we'll use later
$keys = array();
$newArray2 = array();
$myArray2 = array();
// Function to convert CSV into associative array
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
  $newArray2[$j] = $d;
}
// Print it out as JSON
//echo json_encode($newArray);
//print_r($newArray2[0]);
//echo count($newArray2);
for ($i = 0; $i < count($newArray2); $i++){
$myarray2[$i]["sku"] = $newArray2[$i]["sku"];
$myarray2[$i]["afn-fulfillable-quantity"] = $newArray2[$i]["afn-fulfillable-quantity"];
}
//print_r($myarray2);



//echo $myarray2[0]["afn-fulfillable-quantity"];


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