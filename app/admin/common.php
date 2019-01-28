<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

use app\admin\logic\Log as LogicLog;



// //查找归属项目
// function is_porject_banner($card_id)
// {
	
// 	$row=db('project')->where('id = "'.$project_id.'"')->find();

// 	if($row)
// 	{
// 		return $row['gift_name'];
// 	}
// 	else
// 	{
// 		return '无';
// 	}
// }
//selected
 function selected($category_id,$project_id)
{
	$categroy=db('goods_category')->where('tid = 0 and status = 1 and id = "'.$category_id.'"')->find();

	if($categroy['cate_affiliation'] == $project_id)
	{
		return 1;
	}
	return 0;
}
//查找项目
function is_porject_banner($project_id)
{
	$row=db('project')->where('id = "'.$project_id.'"')->find();

	if($row)
	{
		return $row['gift_name'];
	}
	else
	{
		return '无';
	}
}

//查找项目
function is_project($project_id)
{
	$row=db('project')->where('id = "'.$project_id.'"')->find();

	if($row)
	{
		return $row['gift_name'];
	}
	else
	{
		return '';
	}
}
//查找操作人
function operator($id)
{
	$row=db('member')->where('id = "'.$id.'"')->find();

	if($row)
	{
		return $row['nickname'];
	}
	else
	{
		return '正常计费';
	}
}

// //curl请求
// function juhecurl($url,$header,$params=false,$ispost=0){
//      $httpInfo = array();
//    		$ch = curl_init();
// 		curl_setopt($ch, CURLOPT_POST, 1);
// 		curl_setopt($ch, CURLOPT_HEADER,$header);
// 		curl_setopt($ch, CURLOPT_URL,$url);
// 		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
// 		$result = curl_exec($ch);
// 		curl_close($ch);
// 		return $result;
// }

//curl请求
// function juhecurl_get($url,$header,$params=false,$ispost=0){
// 	$httpInfo = array();
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_HEADER,$header);
// 	curl_setopt($ch, CURLOPT_URL,$url);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 	curl_setopt($ch, CURLOPT_NOBODY, false);
	
// 	// curl_exec抓取URL并把它传递给浏览器;
// 	//curl_close($ch)关闭cURL资源，并且释放系统资源

// 	$response = curl_exec($ch);

// 	if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
// 	    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
// 	    $header = substr($response, 0, $headerSize);
// 	    $body = substr($response, $headerSize);
// 	}
// 	else
// 	{
// 		return false;
// 	}
// 	curl_close($ch);

// 	return json_decode($body,true);
// }



//求卡号
function card_hao($card_id)
{
	$row=db('card')->where('id = "'.$card_id.'"')->find();
	return $row['gift_number'];
}

//显示一级分类 
// param int xid 
// return char
function goods_tid($xid)
{
	$row=db('goods_category')->where('id = "'.$xid.'"')->find();
	return $row['cate_name'];
}

//显示一级分类 
// param int tid 
// return char
function goods_default($tid)
{
	$row=db('goods_category')->where('id = "'.$tid.'"')->find();
	if($row['goods_category_default']==1)
	{
		$msg='是';
	}
	else
	{
		$msg='<span style="color:#f00">否</span>';
	}
	return $msg;
}
/**
 * 记录行为日志
 */
function action_log($name = '', $describe = '')
{

    $logLogic = get_sington_object('logLogic', LogicLog::class);
    
    $logLogic->logAdd($name, $describe);
}

/**
 * 清除登录 session
 */
function clear_login_session()
{
    
    session('member_info',      null);
    session('member_auth',      null);
    session('member_auth_sign', null);
}


