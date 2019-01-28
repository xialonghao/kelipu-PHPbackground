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

namespace app\admin\validate;

/**
 * 轮播图验证器
 */
class Banner extends AdminBase
{

    // 验证规则
    protected $rule =   [
        'img_name'          => 'require|max:15',
        // 'content'       => 'require',
        // 'category_id'   => 'require',
    ];

    // 验证提示
    protected $message  =   [
        'img_name.require'         => '轮播图标题不能为空',
        'img_name.max'         => '轮播图标题最大长度15位',
        // 'content.require'      => '文章内容不能为空',
        // 'category_id.require'  => '文章分类必须选择',
    ];

    // 应用场景
    protected $scene = [
        'edit'  =>  ['img_name']
    ];
}
