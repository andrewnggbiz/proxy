<?php
/**
 * 前台主页控制器
 */
class IndexControl extends CommonControl {
	/**
	 * 首页
	 */
	public function index() {
		$this->display ();
	}
	/**
	 * 采集页
	 */
	public function parse() {
		// 类别
		$categorytop = M ( "category" )->where (array( "pid" => 0 ))->select ();
		$this->assign ( "categorytop", $categorytop );
		// 采集
		if (Q ( "post.websiteText" )) {
			$url = Q ( "post.websiteText" );
			$update = Q ( "update" );
			$collect = A ( "Data/Proxy/index", array ( $url, $update ) );
			$this->assign ( "collect", $collect );
			$update == "auto" ? $ufs = 1 : $ufs = 0;
			$collect["auto_disabled"] ? $auto_disabled = 1 : $auto_disabled = 0;
			Q ( "post.ctype" ) == "plural" ? $ctype = 1 : $ctype = 0;
			Q ( "post.collection" ) ? $collection = 1 : $collection = 0;
			$fs = array(
				"update" => $ufs,
				"auto_disabled" => $auto_disabled,
				"ctype" => $ctype,
				"collection" => $collection
			);
			$website = $url;
		} else {
			$website = "请填入需采集的视频地址";
			$fs = array(
				"update" => 0,
				"auto_disabled" => 0,
				"ctype" => 0,
				"collection" => 0
			);
		}
		if (Q ( "get.url" )) {
			$yy_url = A ( "Data/Proxy/collect", array ( Q ( "get.url" ) ) );
			$this->assign ( "yyurl", $yy_url );
		}
		if (! Q ( "session.username" ) && ! Q ( "session.uid" ) && Q ( "session.group" ) != 1){
			$readonly = true;
		}else{
			$readonly = false;
		}
		$this->assign ( "updatefs", $fs );
		$this->assign ( "readonly", $readonly );
		$this->assign ( "website", $website );
		$this->display ();
	}
}
?>