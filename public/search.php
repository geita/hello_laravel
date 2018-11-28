<?php
require_once("php_v2.0.3/CloudsearchClient.php");
require_once("php_v2.0.3/CloudsearchSearch.php");
// 获取搜索框的内容
$query = htmlspecialchars(trim($_GET['q']));
// 实例化一个client，修改掉你的access key id 和secret
$client = new CloudsearchClient("accesskeyid", "secret", array("host" => "http://opensearch.aliyuncs.com"), "aliyun");
// 实例化一个搜索类
$search = new CloudsearchSearch($client);
// 指定一个应用来进行搜索，例如我们刚刚创建好的my_news。
$search->addIndex("my_news");
// 指定我们的搜索关键词。
$search->setQueryString("default:'". $query ."'");
// 指定我们的搜索返回结果格式为json，其他的还有xml,protobuf
$search->setFormat("json");
// 开始搜索，返回结果为一个json的字符串。
$json = $search->search();
// 把json 变成数组
$result = json_decode($json, true);
// 打印搜索结果
// print_r($result);
?>
<html>
<title>最简陋的搜索</title>
<style>
em {color:red}
</style>
</head>
<body>
<form method="get" action="search.php">
<input type=text name="q" value="<?php echo $query ?>" style="width: 500px; height: 28px"> <input type="submit" value="搜索">
</form>
<?php
echo "搜索结果共" . $result['result']['total'] . "条<br><br>";
foreach ($result['result']['items'] as $doc) {
  echo "<a href='{$doc['url']}'>{$doc['title']}</a></br>";
  echo $doc['body'] . "<br>";
  echo "<br>";
}
?>