<?php
/**
 * 风行采集控制器
 */
class FunshionControl extends CommonControl {
	/**
	 * 默认执行
	 */
	public function index() {
		header ( "Content-type:text/xml;charset:utf-8;filename:风行代理.xml" ); // 定义文件头
		echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n"; // 输出XML格式
		if ($id = Q("get.id")) {
			$xml = $this->cache_collect("funshion_" . $id);
			if ($xml != 1 && $xml) {
				echo $xml;
			}else{
				$xml = $this->listpage($id);
				$xml = $xml["xml"];
				if ($cachetime = Q("get.cachetime", null, "intval")){
					$this->cache_collect($id, 1, $xml, "funshion_", "file", $cachetime, ROOT_PATH . "Cache/Auto");
				} else {
					$this->cache_collect($id, 1, $xml, "funshion_");
				}
				echo $xml;
			}
		} elseif ( Q ("get.vname") ) {
			$vName = $this->listpage ( Q ("get.vname") );
			echo $vName["vName"];
		} else {
			$url = file_data ( 'http://www.funshion.com/rank/fs250/' );
			$preg = '/<\/i><a href="(.*)" title="(.*)">.*<\/a><span>/iUs';
			preg_match_all ( $preg, $url, $arr );
			foreach ( $arr [1] as $value ) {
				$lists = $this->listpage ( "http://www.funshion.com" . $value );
				$xml .= $lists["xml_m"];
			}
			echo "<list>\n" . $xml . '</list>';
		}
	}
	/**
	 * 生成列表
	 * @param  string $id 视频ID
	 * @return array     视频列表及视频名称
	 */
	public function listpage($id) {
		$page = file_data ( "http://www.funshion.com/subject/".$id );
		preg_match ( '/window.minfo.webfsps = (.*);/imsU', $page, $info );
		$obj = json_decode ( $info [1] );
		$title = $obj->mp4->name_cn;
		$type = $obj->mp4->displaytype;
		if ($type == "tv") {
			$urls = $obj->mp4->fsps->mult;
			$xml = "";
			foreach ( $urls as $url ) {
				$url = $url->url;
				$url = file_data ( 'http://www.funshion.com' . $url );
				preg_match ( '/playinfo = (.*);/imsU', $url, $arr );
				$url = $arr [1];
				$obj = json_decode ( $url );
				$label = $obj->mp4->description;
				$url = 'http://jobsfe.funshion.com/query/v1/mp4/' . $obj->mp4->hashid . '.json';
				$url = file_data ( $url );
				$obj = json_decode ( $url );
				$xml .= '<m type="2" src="' . $obj->playlist [0]->urls [0] . '?start={start_bytes}" stream="true" label="' . $title . " " . $label . '"/>' . "\n";
			}
		} elseif ($type == "movie") {
			$url = $obj->mp4->fsps->mult [0];
			$url = $url->url;
			$url = file_data ( 'http://www.funshion.com' . $url );
			preg_match ( '/playinfo = (.*);/imsU', $url, $arr );
			$url = $arr [1];
			$obj = json_decode ( $url );
			$label = $obj->mp4->description;
			$url = 'http://jobsfe.funshion.com/query/v1/mp4/' . $obj->mp4->hashid . '.json';
			$url = file_data ( $url );
			$obj = json_decode ( $url );
			$xml = '<m type="2" src="' . $obj->playlist [0]->urls [0] . '?start={start_bytes}" stream="true" label="' . $label . '"/>' . "\n";
		}
		return array("xml" => "<list>\n" . $xml . "</list>", "lists" => $xml, "vName" => $title);
	}
}
?>