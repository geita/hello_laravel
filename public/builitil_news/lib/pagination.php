<?php
/**
 * @usage
 *		$opts = array('perpage' => $perpage, 'current_page' => $page);
 *		$page = new Y_Pagination($count_rows, $url, $opts);
 *		echo $page->getPagination();
 *
 */
class Pagination
{
	private $perpage = 10;
	private $current_page = 0;
	private $num_edge_entries = 0;
	private $num_display_entries = 10;

	private $ellipse_text = '&#8230;';
	private $prev_text = '上一页';
	private $next_text = '下一页';
	private $prev_show = false;
	private $next_show = false;

	private $total;
	private $address;
	private $page_count;

	private static $_params_init = array(
  		'perpage',
		'current_page',
		'num_edge_entries',
		'num_display_entries'
  );

	public function __construct($total, $address, $opts = array())
	{
		foreach (self::$_params_init AS $v)
		{
			if (isset($opts[$v]))
			{
				$this->$v = $opts[$v];
			}
		}

		if ($this->current_page > 0)
		{
    	$this->current_page -= 1;
  	}

  	$this->total = !$total ? 1 : $total;
  	$this->address = $address;
		$this->page_count = ceil($this->total / $this->perpage);
	}

	private function getInterval()
	{
		$ne_half = ceil($this->num_display_entries / 2);
		$start = $this->current_page > $ne_half ? max(min($this->current_page - $ne_half, ($this->page_count - $this->num_display_entries)), 0) : 0;
		$end = $this->current_page > $ne_half ? min($this->current_page + $ne_half, $this->page_count) : min($this->num_display_entries, $this->page_count);

		if ($this->current_page - $start > $ne_half)
		{
			$start = $this->current_page - $ne_half;
		}
		return array($start, $end);
	}

	private function appendItem($page, $appendopts = array())
	{
		$page = $page < 0 ? 0 : ($page < $this->page_count ? $page : $this->page_count - 1);
		$appendopts = array_merge(array('text' => $page + 1, 'classes' => ''), $appendopts);

		if ($page == $this->current_page)
		{
			$lnk = '<span>' . $appendopts['text'] . '</span>';
		}
		else
		{
			if (is_numeric($appendopts['text']))
			{
				//$appendopts['text'] = '[' . $appendopts['text'] . ']';
			}
			$lnk = "<a href='" . $this->address . (strpos($this->address, '?') !== false ? '&amp;' : '?') . "page=" . ($page + 1). "'" . ($appendopts['classes'] ? ' class="' . $appendopts['classes'] . '"' : '') . ">" . $appendopts['text'] . "</a>";
		}
		return $lnk;
	}

	public function getPagination()
	{
		if ($this->total <= $this->perpage)
		{
			return '';
		}
		$interval = $this->getInterval();
		$pagination = '';

		if ($this->prev_text && ($this->current_page > 0 || $this->prev_show))
		{
			$pagination .= $this->appendItem($this->current_page - 1, array('text' => "<b>&lt;</b><label>".$this->prev_text."</label>", 'classes' => 'btn_page'));
		}

		if ($interval[0] > 0 && $this->num_edge_entries > 0)
		{
			$end = min($this->num_edge_entries, $interval[0]);
			for ($i = 0; $i < $end; $i++)
			{
				$pagination .= $this->appendItem($i);
			}

			if ($this->num_edge_entries < $interval[0] && $this->ellipse_text)
			{
				$pagination .= '<span>' . $this->ellipse_text . '</span>';
			}
		}

		for ($i = $interval[0]; $i < $interval[1]; $i++)
		{
			$pagination .= $this->appendItem($i);
		}
		if ($this->next_text && ($this->current_page < $this->page_count - 1 || $this->next_show))
		{
			$pagination .= $this->appendItem($this->current_page + 1, array('text' => "<label>".$this->next_text."</label><b>&gt;</b>", 'classes' => 'btn_page'));
		}
		return $pagination;
	}

	public static function getURLQuery($page_variable = 'page')
	{
		$url       = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
		$parse_url = parse_url($url);
		$query     = isset($parse_url["query"]) ? $parse_url["query"] : null;

		if($query)
		{
			$page      = isset($_GET[$page_variable]) ? $_GET[$page_variable] : "";
			$query     = self::stripURLPageParam($query, $page_variable, $page);
			$url       = str_replace($parse_url["query"], $query, $url);
		}

		return $url;
	}

	public static function stripURLPageParam($url, $key = 'page', $value = 0)
	{
        $value = urlencode($value);
		$url = preg_replace("/(^|&|)$key=$value/", "", $url);

		return $url;
	}

    public static function appendToURL($in_url, $str)
    {
        $str = ltrim($str, "&");
        $in_url .= preg_match('/\?/i', $in_url)
            ? '&'.$str
            : '?'.$str;

        return $in_url;
    }
    public static function isImg($url,$file=""){
        $url2 = $url;
        if (preg_match("/.gif/", $url)){
            return $file;
        }else{
            return $url;
        }

    }

}
