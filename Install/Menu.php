<?php

return array(
    array(
        //父菜单ID，NULL或者不写系统默认，0为顶级菜单
        "parentid" => 37,
        //地址，[模块/]控制器/方法
        "route" => "Wechat/Wechat/index",
        //类型，1：权限认证+菜单，0：只作为菜单
        "type" => 0,
        //状态，1是显示，0不显示（需要参数的，建议不显示，例如编辑,删除等操作）
        "status" => 1,
        //名称
        "name" => "微信",
        //备注
        "remark" => "微信相关操作",
        //子菜单列表
        "child" => array(
            array(
                "route" => "Wechat/Wechat/index",
                "type" => 1,
                "status" => 1,
                "name" => "微信用户",
            ), array(
                "route" => "Wechat/Wechat/appList",
                "type" => 1,
                "status" => 1,
                "name" => "应用信息",
                "child" => array(
                    array(
                        "route" => "Wechat/Wechat/createApp",
                        "type" => 1,
                        "status" => 0,
                        "name" => "创建开放平台应用页面",
                    ),
                    array(
                        "route" => "Wechat/Wechat/doCreateApp",
                        "type" => 1,
                        "status" => 0,
                        "name" => "创建开放平台应用操作",
                    ),

                    array(
                        "route" => "Wechat/Wechat/editApp",
                        "type" => 1,
                        "status" => 0,
                        "name" => "编辑开放平台应用页面",
                    ),
                    array(
                        "route" => "Wechat/Wechat/doEditApp",
                        "type" => 1,
                        "status" => 0,
                        "name" => "编辑开放平台应用操作",
                    ),

                    array(
                        "route" => "Wechat/Wechat/doDeleteApp",
                        "type" => 1,
                        "status" => 0,
                        "name" => "删除开放平台应用操作",
                    ),
                )
            ),array(
                "route" => "Wechat/Wechat/index",
                "type" => 1,
                "status" => 0,
                "name" => "模板信息",
                "child" => array(
                    array(
                        "route" => "Wechat/Wechat/tplMessages",
                        "type" => 1,
                        "status" => 0,
                        "name" => "模板消息列表页面",
                    ),
                    array(
                        "route" => "Wechat/Wechat/createTplMessage",
                        "type" => 1,
                        "status" => 0,
                        "name" => "添加模板消息页",
                    ),
                    array(
                        "route" => "Wechat/Wechat/doCreateTplMessage",
                        "type" => 1,
                        "status" => 0,
                        "name" => "添加模板消息操作",
                    ),
                    array(
                        "route" => "Wechat/Wechat/editTplMessage",
                        "type" => 1,
                        "status" => 0,
                        "name" => "编辑模板消息页",
                    ),
                    array(
                        "route" => "Wechat/Wechat/doEditTplMessage",
                        "type" => 1,
                        "status" => 0,
                        "name" => "编辑模板消息操作",
                    ),
                    array(
                        "route" => "Wechat/Wechat/doDeleteTplMessage",
                        "type" => 1,
                        "status" => 0,
                        "name" => "删除模板消息操作",
                    ),
                )
            ),

        ),
    ),
);
