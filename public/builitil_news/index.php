<?php
header("Content-Type:text/html;charset=utf-8");
define("DS", DIRECTORY_SEPARATOR);
define("ROOT_DIR", dirname(__FILE__));
ini_set("display_errors", 0);
require_once(ROOT_DIR . DS ."lib" . DS . 'config.php');
require_once(ROOT_DIR . DS . SDK_VERSION . DS . 'CloudsearchClient.php');
require_once(ROOT_DIR . DS . SDK_VERSION . DS . 'CloudsearchSearch.php');
require_once(ROOT_DIR . DS . "lib" . DS . 'pagination.php');
require_once(ROOT_DIR . DS . "lib" . DS . 'infosearch.php');


$url_query = Pagination::getURLQuery();

try {
    $client = new CloudsearchClient(
        ACCESSKEYID,
        SECRET,
        array('host' => 'http://opensearch-cn-qingdao.aliyuncs.com'),
        KEY_TYPE
    );

    $search = new CloudsearchSearch($client);

    //设置要搜索的应用名称：
    $search->addIndex(APP_NAME);

    //获取请求参数
    $param = InfoSearch::loadParam();
    // echo '<pre>';
    // print_r($param);
    // exit;

    //设置搜索参数
    InfoSearch::buildSearchParam($param,$search);
     //dd($search->search());
    //获取搜索结果。
    $search_result = json_decode($search->search(),true);

    $result = $search_result["result"];
    //dd($result);
} catch (Exception $e) {
    // pass
}
//分页
$total = isset($result['total']) ? $result['total'] : 0;
$view_total = isset($result['viewtotal']) ? $result['viewtotal'] : 0;
$opts = array('perpage' => InfoSearch::PAGE_SIZE, 'current_page' => $param["page"]);
$pagination = new Pagination($view_total, $url_query, $opts);
$pagination_str = $pagination->getPagination();
?>

<!doctype html>
<html lang="zh-cn">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta content="width=device-width,maximum-scale=1.0,minimum-scale=1.0" name="viewport"/>
        <meta name="format-detection" content="telephone=no"/>
        <title>资讯模版样式</title>
        <link rel="stylesheet" type="text/css" href="./index.css" />
        <style type="text/css">
            em{
                color: red;
                font-style: normal;
            }
            .red{
                color:red;
            }
            .aly-contentWrap .results .desc {
                font-size: 12px;
            }
            .aly-contentWrap  .other {
                color: #888;
            }
            .aly-contentWrap .title a {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div id="aly-wrap">
            <form>
                <div class="aly-searchWrap">
                    <div class="aly-search inner">
                        <div class="navArea clearfix">
                            <div  class="shLeft fl">

                            </div>
                            <div class="shCtn fl"><input id="alySearch" autocomplete="on" name="q" type="text" value="<?php echo htmlspecialchars($param["q"], ENT_QUOTES); ?>"/></div>
                            <div class="shBtn fl"><input id="alySmt" type="submit" value="搜索"  name="sub"/></div>
                        </div>
                    </div>
                </div>
                <div class="aly-sep"></div>
                <div class="aly-contentWrap inner clearfix">
                    <div class="fltBox fl">
                        <dl class="fltList" id="fltListC">
                            <dt>资讯分类</dt>
                            <dd>
                                <div id="alySel" class="shSel"><span id="selType" val="all">全部</span><input name="search_field" id="selVal" type="hidden" value="0"/>
                                    <ul id="alyTog" class="toggle">
                                        <?php
                                        foreach (InfoSearch::$search_fields as $k => $v) {

                                            if ($k != $param['search_field']) {
                                                ?>
                                                <li><a val="<?php echo $k; ?>" href="javascript:void(0)"><?php echo $v; ?></a></li>

                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </dd>
                            <dt>排序</dt>
                            <dd>
                                <span class="floor"><input name="sort" value="0" <?php if ($param["sort"] == 0) echo "checked"; ?> type="radio" /><label>按相关性</label></span><br/>
                                <span class="floor"><input name="sort" value="1" <?php if ($param["sort"] == 1) echo "checked"; ?>  type="radio" /><label>按时间</label></span>
                            </dd>
                            <dt>时间筛选</dt>
                            <dd>
                                <span class="floor"><input value="1" name="time_order"<?php echo ($param['time_order'] == 1) ? "checked" : "" ?> 	type="radio" /><label>一小时内</label></span><br/>
                                <span class="floor"><input value="2" name="time_order"  <?php echo ($param['time_order'] == 2) ? "checked" : "" ?> 	type="radio" /><label>一天内</label></span><br/>
                                <span class="floor"><input value="3"  name="time_order" <?php echo ($param['time_order'] == 3) ? "checked" : "" ?> 	type="radio" /><label>一个月内</label></span><br/>
                                <span class="floor"><input value="4"  name="time_order" <?php echo ($param['time_order'] == 4) ? "checked" : "" ?> 	type="radio" /><label>一年内</label></span><br/>
                                <span class="floor"><input value="0"  name="time_order" <?php echo ($param['time_order'] == 0) ? "checked" : "" ?>  	type="radio" /><label>全部</label></span>
                            </dd>
                        </dl>
                    </div>
                    <div class="contentBox fl">
                        <span class="rstNum">用了
                            <?php echo isset($result["searchtime"]) ? $result["searchtime"] : 0; ?>秒，云搜索为您找到<em class="qw"><?php echo htmlspecialchars($param["q"], ENT_QUOTES); ?></em>相关作品<?php echo isset($result["total"]) ? $result["total"] : 0; ?>部</span>
                        <ul class="results" id="results">
                            <?php
                            if (isset($result["items"]) && $result["items"]) {
                                $items = $result["items"];
                                foreach ($items as $row) {
                                    ?>
                                    <li class="record"><a href="#del:1" class="ico_del"></a>
                                        <h3 class="title"><a href="<?php echo isset($row['url']) ? $row['url'] : ""; ?>"     target="_blank" ><?php echo isset($row["title"]) ? $row["title"] : NULL; ?></a></h3>

                                        <?php if (isset($row["thumbnail"]) && $row["thumbnail"] != "") { ?>
                                            <table cellpadding="0" cellspacing="0" border="0" class="box">
                                                <tbody><tr>
                                                        <td class="imgbox2" rowspan="2" width="80"><img src="<?php echo $row["thumbnail"]; ?>" onload="this.parentNode.className='imgbox2'" onerror="this.parentNode.style.display='none';"  ></td>
                                                        <td><div class="desc"><?php echo isset($row["body"]) ? $row["body"] : NULL; ?></div></td>
                                                    </tr>
                                                </tbody></table>

                                        <?php } else { ?>
                                            <div class="desc"><?php echo isset($row["body"]) ? $row["body"] : NULL; ?></div>
                                    <?php } ?>
                                            <span class="other">来源：<?php echo isset($row['source']) ? $row['source'] : "";
                            echo "&nbsp&nbsp";
                            echo isset($row['create_timestamp']) ? date('Y-m-d H:i:s', $row['create_timestamp']) : "";  echo "&nbsp;&nbsp;";

                            ?> </span>
                                    </li>
    <?php }
} ?>
                        </ul>

                    </div>

                </div>
                <div class="aly-paging">
                    <div class="page inner">
<?php
echo isset($pagination_str) ? $pagination_str : NULL;
?>
                    </div>
                </div>
                <div class="aly-cpRight">Copyright © 2014 阿里云计算版权所有 不得转载  <a class="cpLink" href="http://help.opensearch.aliyun.com/" target="_blank">使用须知</a>   <a class="cpLink" href="http://www.aliyun.com/"  target="_blank">关于阿里云</a>
            <br />
            Powered by Aliyun
                </div>
            </form>
        </div>



        <script>

            setTimeout(function(){
                document.getElementById("selVal").value = "<?php echo $param["search_field"] ?>";
                document.getElementById("selType").innerHTML = "<?php echo InfoSearch::$search_fields[$param["search_field"]]; ?>";
                document.getElementById("alySel").onclick = function(){
                    if(this.className.indexOf("selShow") == -1){
                        this.className += " selShow";
                    }else{
                        this.className = this.className.replace("selShow","");
                        var _ele = window.event.target||window.event.srcElement;
                        if(!_ele.id ){
                            while(_ele.tagName != "A"){
                                _ele = _ele.parentNode;
                            }

                            if(_ele && _ele.tagName == "A"){
                                var _val = _ele.getAttribute("val"),
                                _selEle = document.getElementById("selVal"),
                                _liEle = document.getElementById("selType"),
                                _eleVal = _ele.innerHTML;
                                if(_val){
                                    _ele.setAttribute("val",_selEle.value);
                                    _ele.innerHTML = _liEle.innerHTML;

                                    _selEle.value = _val;

                                    _liEle.innerHTML = _eleVal;
                                }
                            }else{
                                return;
                            }

                        }

                    }
                };

            },1);
            document.getElementById("fltListC").onclick = function(){
                var _ele = window.event.target||window.event.srcElement;
                if(_ele && _ele.type && _ele.type.toLowerCase() == "radio"){
                    document.forms[0].submit();
                }
            }
        </script>
    </body>
</html>
