<?php
/**
 * 后台用户管理控制器
 */
class AdminControl extends CommonControl {
	/**
	 * 用户列表
	 */
	public function index() {
		$field = "aid,username,logintime,loginip,userlock";
		$this->assign ( "admin", M ( "admin" )->field ( $field )->select () );
		$this->display ();
	}
	/**
	 * 锁定用户
	 */
	public function lock() {
		$aid = $this->_get ( "aid", "intval" );
		M ( "admin" )->where ( array ( "aid" => $aid ) )->save ( array ( "userlock" => 1 ) );
		$this->success ( "锁定成功！" );
	}
	/**
	 * 解锁用户
	 */
	public function unlock() {
		$aid = $this->_get ( "aid", "intval" );
		M ( "admin" )->where ( array ( "aid" => $aid ) )->save ( array ( "userlock" => 0 ) );
		$this->success ( "解锁成功！" );
	}
	/**
	 * 添加后台用户
	 */
	public function add() {
		if (IS_POST) {
			$passwdF = $this->_post ( "passwdF", "trim,htmlspecialchars" );
			$passwdS = $this->_post ( "passwdS", "trim,htmlspecialchars" );
			if (strlen ( $passwdF ) < 6)
				$this->error ( "密码不足6位" );
			if ($passwdF != $passwdS)
				$this->error ( "两次密码不同" );
			$username = $this->_post ( "username" );
			$stat = M ( "admin" )->field ( "aid" )->where ( array ( "username" => $username ) )->find();
			if ($stat)
				$this->error ( "用户已经存在，请更换用户名！" );
			$data = array (
					"username" => $username,
					"passwd" => md5 ( $passwdF ) 
			);
			M ( "admin" )->add ( $data );
			$this->success ( "添加成功！" );
		}
		$this->display ();
	}
}