<?php
require_once "curl.php";

$baseUrl = "https://www.lianquan.org/";
$url = "https://www.lianquan.org/ContractPlus_SeriesInfo?guid=F138D3BCD709FB3D696B64964376211BE566C1C241448A920BBC9E7780442A77DBD9A035A02A3FCA1225E9B60B589C25";
$html =  httpGet($url);

$dom = new DOMDocument();
$dom->loadHTML($html);
// $dom->loadHTMLFile('htmlfile.html');

// 访问 document element 元素
$documentElement = $dom->documentElement;

// 根据ID获取元素内容
$header = $dom->getElementById('header'); // DOMElement
$child_elements = $header->getElementsByTagName('li'); // DOMNodeList
$row_count = $child_elements->length - 1;
$li_content = trim($child_elements->item(1)->nodeValue);
echo $li_content."\n";

// 通过标签名来选择
// Both the DOMDocument and DOMElement classes have the method getElementsByTagName()
$h4s = $dom->getElementsByTagName('h4');
foreach ($h4s as  $h4) {
    echo trim($h4->textContent)."\n";
}

// 使用 xpath 来查找节点
// 使用 getAttribute() 方法来获取，使用 hasAttribute() 来判断
$xpath = new DOMXPath($dom);
// $items = $xpath->query('//*[@id="publishingagency"]//div[contains(@class,"item")]');
$items = $xpath->query('//*[@id="publishingagency"]//div[@class="item"]/a');
echo "找到了{$items->length}个元素！\n";
foreach($items as $item) {
    echo trim($item->textContent)."\n";
    $url = $baseUrl.$item->getAttribute("href");
    echo $url."\n";
}
