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
 * API分组验证器
 */
class ApiGroup extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        'name'          => 'require|unique:api_group|max:20',
    ];

    // 验证提示
    protected $message  =   [
        'name.require'         => '分组名称不能为空',
        'name.unique'          => '分组名称已经存在',
        'name.max'          => '分组名称最大长度20位',
    ];
    
    // 应用场景
    protected $scene = [
        'edit'  =>  ['name'],
    ];
}
