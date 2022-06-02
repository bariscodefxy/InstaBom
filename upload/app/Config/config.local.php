<?php
    return array(

        //Project Variables
        "project"  => array(
            "cookiePath"        => "./app/Cookies/",
            "licenseKey"        => "LICENSE_KEY",
            "onlyHttps"         => TRUE,
            "adminPrefix"       => "/admin",
            "resellerPrefix"    => "/bayi",
            "memberLoginPrefix" => "/member"
        ),

        //App Variables
        "app"      => array(
            "theme"                 => "default",
            "layout"                => "layout/default",
            "language"              => "en",
            "base_url"              => NULL,
            "handle_errors"         => TRUE,
            "log_errors"            => FALSE,
            "router_case_sensitive" => TRUE
        ),


        //Database Variables
        "database" => array(
            "DefaultConnection" => array(
                //mysql, sqlsrv, pgsql are tested connections and work perfect.
                "driver"   => "mysql",
                "host"     => "127.0.0.1",
                "port"     => "3306",
                "name"     => "sorunsuzscript",
                "user"     => "sorunsuzscript",
                "password" => "sorunsuzscript"
            )
        )
    );