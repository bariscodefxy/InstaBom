<?php
    return array(
        //Routes
        "MemberLoginRoute"  => array(
            $this->get("project/memberLoginPrefix") . "(/@action(/@id))",
            array(
                "prefix"     => "",
                "controller" => "Member",
                "action"     => "Index"
            )
        ),
        "BayiRoute"         => array(
            $this->get("project/resellerPrefix") . "(/@controller(/@action(/@id)))",
            array(
                "prefix"     => "Bayi",
                "controller" => "Home",
                "action"     => "Index"
            )
        ),
        "AdminPluginsRoute" => array(
            $this->get("project/adminPrefix") . "/plugins" . "(/@controller(/@action(/@id)))",
            array(
                "prefix"     => "Admin/Plugins",
                "controller" => "Home",
                "action"     => "Index"
            )
        ),
        "AdminRoute"        => array(
            $this->get("project/adminPrefix") . "(/@controller(/@action(/@id)))",
            array(
                "prefix"     => "Admin",
                "controller" => "Home",
                "action"     => "Index"
            )
        ),
        "UserDetailRoute"   => array(
            "/user/@usernameid(/@action)",
            array(
                "prefix"     => "",
                "controller" => "User",
                "action"     => "Index"
            )
        ),
        "BlogDetailRoute"   => array(
            "/blog/@seolink",
            array(
                "prefix"     => "",
                "controller" => "Blog",
                "action"     => "BlogDetail"
            )
        ),
        "PluginsRoute"      => array(
            "/plugins(/@controller(/@action(/@id)))",
            array(
                "prefix"     => "Plugins",
                "controller" => "Home",
                "action"     => "Index"
            )
        ),
        "DefaultRoute"      => array(
            "(/@controller(/@action(/@id)))",
            array(
                "prefix"     => "",
                "controller" => "Home",
                "action"     => "Index"
            )
        )
    );