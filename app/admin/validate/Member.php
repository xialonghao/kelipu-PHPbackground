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
 * 会员验证器
 */
class Member extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'username'      => 'require|max:16',
        'nickname'      => 'require|max:16',
        'password'      => 'require|confirm|length:6,20',
        'email'         => 'require|email|max:40',
        'nickname'      => 'require',
        'mobile'        => 'require',
        'old_password'  => 'require',
    ];
    
    // 验证提示
    protected $message  =   [
        
        'username.require'      => '用户名不能为空',
         'username.max'      => '用户名最大长度16位',
        // 'username.unique'       => '用户名已存在',
        'nickname.require'      => '昵称不能为空',
        
        'password.require'      => '密码不能为空',
        'password.confirm'      => '两次密码不一致',
        'password.length'       => '密码长度为6-20字符',
        'email.require'         => '邮箱不能为空',
        'email.email'           => '邮箱格式不正确',
        'email.max'           => '邮箱最大长度为40位',
        // 'email.unique'          => '邮箱已存在',
        'mobile.require'         => '手机号必填',
        'nickname.max'      => '昵称最大长度16位',
      
        'old_password.require'  => '旧密码不能为空',
    ];

    // 应用场景
    protected $scene = [
        
        'add'       =>  ['username','password','email','mobile','nickname'],
        'edit'      =>  ['username','nickname','email','mobile'],
        'password'  =>  ['password','old_password'],
        'passwords'  => ['password']
    ];
}
