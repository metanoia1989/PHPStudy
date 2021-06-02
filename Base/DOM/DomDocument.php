<?php
require_once "curl.php";

$url = "https://www.lianquan.org/ContractPlus_SeriesInfo?guid=F138D3BCD709FB3D696B64964376211BE566C1C241448A920BBC9E7780442A77DBD9A035A02A3FCA1225E9B60B589C25";
$html =  httpGet($url);

$dom = new DOMDocument();
$dom->loadHTML($html);
// $dom->loadHTMLFile('htmlfile.html');

// 访问 document element 元素
$documentElement = $dom->documentElement;

echo json_encode($html);