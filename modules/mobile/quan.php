<?php/** * lionfish 商城系统 * * ========================================================================== * @link      http://www.zx-xcx.com/ * @copyright Copyright (c) 2015 zx-xcx.com.  * @license   http://www.zx-xcx.com/license.html License * ========================================================================== * * @author wane_x * 发现api */if (!defined('IN_IA')) {	exit('Access Denied');}class Quan_Snailfishshop extends MobileController {    public function main()	{		global $_W;		global $_GPC;		echo json_encode( array('code' =>0) );		die();	}		/**		判断是否有发布圈子的权限	**/	public function get_quan_authority()	{		global $_W;		global $_GPC;				$token = $_GPC['token'];		$token_param = array();		$token_param[':uniacid'] = $_W['uniacid'];		$token_param[':token'] = $token;		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);	    $member_id = $weprogram_token['member_id'];	    $member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id limit 1", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));		if( empty($member_info) )		{			echo json_encode( array('code' => 1) );			die();		}				$group_info = pdo_fetch("select * from ".tablename('lionfish_comshop_group')." where uniacid=:uniacid and seller_id=:seller_id limit 1", array(':uniacid' => $_W['uniacid'], ':seller_id' => 1 ));		if( empty($group_info) )		{			echo json_encode( array('code' => 1,'msg'=>'未开放圈子功能') );			die();		}				if($group_info['status'] == 0)		{			echo json_encode( array('code' => 1,'msg'=>'未开放圈子功能') );			die();		}		if($group_info['limit_send_member'] == 1)		{			$member_ids = explode(',', $group_info['member_ids']);						if( empty($member_ids) || !in_array($member_id, $member_ids) )			{				echo json_encode( array('code' => 1,'msg'=>'没有发布权限') );				die();			}		}				echo json_encode( array('code' => 0) );		die();			}		/**		发布帖子	**/	public function post_group()	{		global $_W;		global $_GPC;				$token = $_GPC['token'];		$token_param = array();		$token_param[':uniacid'] = $_W['uniacid'];		$token_param[':token'] = $token;		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);	    $member_id = $weprogram_token['member_id'];	    $member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id limit 1", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));				if( empty($member_info) )		{			echo json_encode( array('code' => 1,'msg' => '您未登录') );			die();		}				$data = $_GPC;				$group_id = $data['group_id'];		$goods_id = $data['goods_id'];		$is_share = $data['is_share'];		$content = htmlspecialchars($data['content']);		$pics = $data['pics'];				$post_data['uniacid'] = $_W['uniacid'];		$post_data['member_id'] = $member_id;		$post_data['group_id'] = $group_id;		$post_data['goods_id'] = $goods_id;		$post_data['title'] = $content;		$post_data['is_share'] = $is_share;		$post_data['content'] = serialize($pics);				$rs = load_model_class('quan')->send_group_post($post_data);				if($rs)		{			echo json_encode( array('code' => 0) );			die();		} else{			echo json_encode( array('code' => 1) );			die();		}	}			/**		获取圈子信息	**/	public function get_quan_info()	{		global $_W;		global $_GPC;				$token = $_GPC['token'];		$seller_id = $_GPC['seller_id'] ? $_GPC['seller_id'] : 1;		$param = array();		$param[':uniacid'] = $_W['uniacid'];		$param[':seller_id'] = $seller_id;				$group_info = pdo_fetch("select * from ".tablename('lionfish_comshop_group')." where uniacid=:uniacid and seller_id=:seller_id ", $param);				if( empty($group_info) )		{			echo json_encode( array('code' => 1) );			die();		}				$data = array();		$data['group_id'] = $group_info['id'];		$data['quan_name'] = $group_info['title'];		$data['post_count'] = $group_info['post_count'];		$data['status'] = $group_info['status'];		$data['quan_share'] = $group_info['quan_share'];		$data['quan_logo'] = tomedia($group_info['quan_logo']);		$data['quan_banner'] = tomedia($group_info['quan_banner']);				echo json_encode( array('code' =>0 , 'data' => $data) );		die();			}		/**		赞帖子/取消赞帖子	**/	public function member_fav_post()	{		global $_W;		global $_GPC;		$token = $_GPC['token'];		$param = array();		$param[':uniacid'] = $_W['uniacid'];		$param[':token'] = $token;		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token", $token_param);	    $member_id = $weprogram_token['member_id'];				if( empty($member_id) )		{			echo json_encode( array('code' => 1,'msg' => '您未登录') );			die();		}				//code = 1 喜欢成功 code =2 取消喜欢		$post_id = isset($_GPC['post_id']) ? $_GPC['post_id'] : 1;		$res = load_model_class('quan')->member_fav_post($member_id,$post_id);		echo json_encode( $res );		die();	}		/**		对帖子进行评价	**/	public function comment_group_post()	{		global $_W;		global $_GPC;		$token = $_GPC['token'];		$param = array();		$param[':uniacid'] = $_W['uniacid'];		$param[':token'] = $token;		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token", $token_param);	    $member_id = $weprogram_token['member_id'];				if( empty($member_id) )		{			echo json_encode( array('code' => 1,'msg' => '您未登录') );			die();		}					$data = $_GPC;				$post_id = $data['post_id'];		$content = $data['content'];		$to_member_id = $data['reply_id'];				if( empty($post_id) )		{			echo json_encode( array('code' => 1, 'msg' => '数据错误，未选择帖子') );//未登录			die();		}				if( empty($content) )		{			echo json_encode( array('code' => 1, 'msg' => '请填写评价内容') );//未登录			die();		}				$last_post_id = load_model_class('quan')->comment_group_post($post_id,$content,$member_id,$to_member_id);				echo json_encode( array('code' => 0,'member_id' => $member_id,'last_post_id' => $last_post_id) );		die();	}		/**		删除评论	*/	public function del_post_comment()	{		global $_W;		global $_GPC;		$token = $_GPC['token'];		$param = array();		$param[':uniacid'] = $_W['uniacid'];		$param[':token'] = $token;		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token", $token_param);	    $member_id = $weprogram_token['member_id'];				if( empty($member_id) )		{			echo json_encode( array('code' => 1,'msg' => '您未登录') );			die();		}				$post_id = $_GPC['post_id'];		$comment_id = $_GPC['comment_id'];		$rs = pdo_delete('lionfish_comshop_group_lzl_reply', array('post_id' => $post_id,'id' => $comment_id,'member_id' => $member_id));				if($rs)		{			echo json_encode(array('code' => 0));			die();		}else{			echo json_encode(array('code' => 1, 'msg' =>'删除失败' ));			die();			}					}		/**		加载数据		@param group_id 群组id		@param post_id 帖子id		@param up_down 加载方向：1 底部加载， 2顶部加载		@param limit 加载10	**/	public function load()	{		global $_W;		global $_GPC;		$group_id = isset($_GPC['group_id']) ? $_GPC['group_id'] : 1;		$post_id = isset($_GPC['post_id']) ? $_GPC['post_id'] : 0;		$up_down = isset($_GPC['up_down']) ? $_GPC['up_down'] : 1;		$ht_s = isset($_GPC['ht_s']) ? $_GPC['ht_s'] : 1;		$limit = 10;				$list = load_model_class('quan')->load_group_post($group_id,$post_id,$up_down,10);				if( empty($list) )		{			echo json_encode( array('code' => 1) );			die();		} else{			reset($list);			if($up_down == 1)			{				$end = current($list);				$first = end($list);			}else{				$end = current($list);				$first = end($list);			}			$this->ht_s = $ht_s/3;						echo json_encode( array('code' => 0,'down_post_id'=>$first['id'],'up_post_id' => $end['id'] ,'list' => $list) );			die();		}	}		/**		发布页加载商品列表数据		@param id 商家id		@param limit 加载10	**/	public function load_fabu_goods()	{		global $_W;		global $_GPC;		$pre_page = 10;		$data = $_GPC;				$page = isset($data['page']) ? $data['page'] : 1;		$seller_id = isset($data['id']) ? $data['id'] : 0;				$condition= " grounding =1 and type='normal' and total > 0 ";		$offset = ($page -1) * $pre_page;		$order_by = 'index_sort asc';		$where = $condition . ' and uniacid='.$_W['uniacid'];		$fileds = 'id,total,seller_count,sales,goodsname';		$list = load_model_class('pingoods')->get_goods_list($fileds, $where ,$order_by,$offset,$pre_page);		foreach($list as $key => $val)        {			$val['goods_id'] = $val['id'];			$good_image = load_model_class('pingoods')->get_goods_images($val['id']);			if( !empty($good_image) )			{				$val['image'] = tomedia($good_image['image']);			}						$price_arr = load_model_class('pingoods')->get_goods_price($val['goods_id']);			$val['danprice'] = $price_arr['price'];			$val['price'] = $price_arr['price'];			$val['fav_goods'] = load_model_class('pingoods')->fav_goods_count($val['id']);			$val['name'] = $val['goodsname'];				$val['seller_count'] += $val['sales'];				$val['quantity'] = $val['total'];			            $need_data[$key] = $val;        }		if( empty($need_data) )		{			echo json_encode( array('code' => 1) );			die();		} else {			echo json_encode( array('code' => 0, 'list' => $need_data) );			die();		}	}		public function get_user_info()	{		global $_W;		global $_GPC;		$token = $_GPC['token'];		$token_param = array();		$token_param[':uniacid'] = $_W['uniacid'];		$token_param[':token'] = $token;		$weprogram_token = pdo_fetch("select member_id from ".tablename('lionfish_comshop_weprogram_token')." where uniacid=:uniacid and token=:token limit 1", $token_param);	    $member_id = $weprogram_token['member_id'];	    $member_info = pdo_fetch("select * from ".tablename('lionfish_comshop_member')." where uniacid=:uniacid and member_id=:member_id limit 1", array(':uniacid' => $_W['uniacid'], ':member_id' => $member_id ));				echo json_encode( array('code' => 0,'member_level_list' => $member_level_list,'level_name' => $level_name,'member_level_is_open' => $member_level_is_open_arr['value'],'is_yue_open' => $config_name['value'], 'opencommiss' => $opencommiss,'data' =>$member_info ,'is_open_integral' => $is_open_integral) );		die();	}}