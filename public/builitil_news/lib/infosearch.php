<?php

class InfoSearch {

    const CSS_URL = "http://opensearch.console.aliyun.com/static/css/search/template/info/info.css";
    const PAGE_SIZE = 10;

    //分类
    public static $type_id = array(
        0 => "全部",
        1 => "新闻",
        2 => "财经",
        3 => "体育",
    );
    public static $sorts = array(
        0 => "相关度",
        1 => "时间",
    );
    //板块
    public static $search_fields = array(
        0 => "全部",
        1 => "资讯标题",
       // 2 => "资讯内容"
    );

    public static $time_order = array(
        0 => "全部时间",
        1 => "一小时内",
        2 => "一天内",
        3 => "一个月内",
        4 => "一年内",
    );

    public static function loadParam() {
        $param = array();
        $param["q"] = isset($_GET["q"]) ? $_GET["q"] : "云搜索";
        $param["sub"] = isset($_GET["sub"]) ? $_GET["sub"] : 0;
        $param["page"] = isset($_GET["page"]) ? $_GET["page"] : 1;
        $param["sort"] = isset($_GET["sort"]) ? $_GET["sort"] : 0;
        $param["type_id"] = isset($_GET["type_id"]) ? $_GET["type_id"] : 0;
        $param["time_order"] = isset($_GET["time_order"]) ? $_GET["time_order"] : 0;
        $param["search_field"] = isset($_GET["search_field"]) ? $_GET["search_field"] : 0;
        return $param;
    }

    public static function buildLinkUrls($param, $url_query) {
        $link_urls = array();
        $link_urls["type_id"] = Pagination::stripURLPageParam($url_query, "type_id", $param["type_id"]);
        $link_urls["sort"] = Pagination::stripURLPageParam($url_query, "sort", $param["sort"]);
        //$link_urls["cat_id"] = Pagination::stripURLPageParam($url_query, "cat_id", $param["cat_id"]);
        $link_urls["time_order"] = Pagination::stripURLPageParam($url_query, "time_order", $param["time_order"]);
        return $link_urls;
    }

    public static function buildSearchParam($param = array(),&$search) {
        if (!$param) {
            $param = self::loadParam();
        }

        //设置查询关键词
        self::buildQuery($param, $search);
        //设置排序规则
        self::buildSort($param, $search);
        //设置搜索过滤条件
        self::buildFilter($param, $search);

        // 指定搜索返回的格式。
        $search->setFormat('json');
        //设置返回结果起始位置
        $search->setStartHit(((int)$param["page"]-1)*InfoSearch::PAGE_SIZE);
        //设置返回结果条数
        $search->setHits(InfoSearch::PAGE_SIZE);

    }

    private static function buildFilter($param, &$search) {

        if ($param["type_id"]) {
            $search->addFilter("type_id = " . $param["type_id"],"AND");
        }

        $now_time = time();
        $now_hour = strtotime(date("Y-m-d H").":00:00");
        $now_day = strtotime(date("Y-m-d")." 00:00:00");
        $now_month = strtotime(date("Y-m")."-1 00:00:00");
        $now_year = strtotime(date("Y")."-1-1 00:00:00");
        $start_timestamp = "";
        switch ($param["time_order"]) {
            case 1 : {
                    $start_timestamp =  "create_timestamp >= ".$now_hour  ;
                    break;
                }
            case 2 : {
                    $start_timestamp = "create_timestamp >= ".$now_day ;
                    break;
                }
            case 3 : {
                    $start_timestamp = "create_timestamp >= ".$now_month;
                    break;
                }
            case 4 : {
                    $start_timestamp = "create_timestamp >= ".$now_year;
                    break;
                }
            default : {

                }

        }
        if($start_timestamp != ""){
            $search->addFilter('create_timestamp >='.$start_timestamp,"AND");
        }
    }

    private static function buildQuery($param, &$search) {
           switch ($param["search_field"])
        {
            case 1 :
            {
                $search->setQueryString("title:'".$param['q']."'");
                break;
            }
            case 2 : {
                $search->setQueryString("body:'".$param['q']."'");
                break;
            }
            case 0 :
            {
                $search->setQueryString("default:'".$param['q']."'");
                break;
            }
        }

        return true;
    }

    private static function buildSort($param, &$search) {
        switch ($param["sort"]) {
            case 1 : {
                    $search->addSort('create_timestamp',"-");
                    break;
                }
            case 2 : {
                    $search->addSort('hits',"-");
                    break;
                }
            case 3 : {
                    $search->addSort('update_timestamp',"-");
                    break;
                }
            case 4 : {
                    $search->addSort('replies',"-");
                    break;
                }
            case 0:
            default : {
                }
        }

        return true;
    }

}

//end class
?>
