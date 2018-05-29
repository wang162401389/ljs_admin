<?php

namespace addons\blog;

use app\common\library\Menu;
use think\Addons;

/**
 * 博客插件
 */
class Blog extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'blog',
                'title'   => '博客管理',
                'sublist' => [
                    [
                        'name'    => 'blog/post',
                        'title'   => '日志管理',
                        'sublist' => [
                            ['name' => 'blog/post/index', 'title' => '查看'],
                            ['name' => 'blog/post/add', 'title' => '添加'],
                            ['name' => 'blog/post/edit', 'title' => '修改'],
                            ['name' => 'blog/post/del', 'title' => '删除'],
                            ['name' => 'blog/post/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'blog/category',
                        'title'   => '分类管理',
                        'sublist' => [
                            ['name' => 'blog/category/index', 'title' => '查看'],
                            ['name' => 'blog/category/add', 'title' => '添加'],
                            ['name' => 'blog/category/edit', 'title' => '修改'],
                            ['name' => 'blog/category/del', 'title' => '删除'],
                            ['name' => 'blog/category/multi', 'title' => '批量更新'],
                        ]
                    ],
                    [
                        'name'    => 'blog/comment',
                        'title'   => '评论管理',
                        'icon'    => 'fa fa-comment',
                        'sublist' => [
                            ['name' => 'blog/comment/index', 'title' => '查看'],
                            ['name' => 'blog/comment/add', 'title' => '添加'],
                            ['name' => 'blog/comment/edit', 'title' => '修改'],
                            ['name' => 'blog/comment/del', 'title' => '删除'],
                            ['name' => 'blog/comment/multi', 'title' => '批量更新'],
                        ]
                    ]
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('blog');
        return true;
    }
    
    /**
     * 插件启用方法
     */
    public function enable()
    {
        Menu::enable('blog');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        Menu::disable('blog');
    }

}
