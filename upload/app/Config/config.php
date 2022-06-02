<?php

    return array(

        //Project Variables
        "project"  => array(
            "cookiePath"        => "./app/Cookies/",
			"cronJobToken"      => "teRv-TEac-sa41-h8t9-x444",
            "onlyHttps"         => FALSE,
            "adminPrefix"       => "/admin",
            "resellerPrefix"    => "/bayi",
            "memberLoginPrefix" => "/giris"
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
                "host"     => "localhost",
                "port"     => "3306",
                "name"     => "sorunsuzscript",
                "user"     => "sorunsuzscript",
                "password" => "sorunsuzscript"
            )
        )
    );