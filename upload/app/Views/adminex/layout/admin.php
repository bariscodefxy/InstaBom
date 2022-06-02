<?php
    /**
     * Wow Master Template
     *
     * @var \Wow\Template\View      $this
     * @var \App\Models\LogonPerson $logonPerson
     * @var string                  $controllerName
     * @var string                  $actionName
     */
    $logonPerson = $this->get("logonPerson");
    if(!$logonPerson->isLoggedIn()) {
        return;
    }
    $uyelik         = $logonPerson->member;
    $controllerName = $this->route->params["controller"];
    $actionName     = $this->route->params["action"];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php $this->section("section_head"); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/style/font-awesome.min.css" type="text/css" media="screen"/>
    <link href="/assets/themes/adminex/css/style.css?v=1.1" rel="stylesheet">
    <link href="/assets/themes/adminex/css/style-responsive.css" rel="stylesheet">
    <link rel="shortcut icon" href="/assets/images/favicon.png" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="/assets/bootstrap-datepicker/css/datepicker-custom.css"/>
    <title><?php if($this->has('title')) {
            echo $this->get('title') . " | ";
        }
            echo Wow::get("ayar/site_title"); ?></title>
    <?php if($this->has('description')) { ?>
        <meta name="description" content="<?php echo $this->get('description'); ?>"><?php } ?>
    <?php if($this->has('keywords')) { ?>
        <meta name="keywords" content="<?php echo $this->get('keywords'); ?>"><?php } ?>
    <?php $this->show(); ?>
</head>
<body class="sticky-header">
<section>
    <!-- left side start-->
    <div class="left-side sticky-left-side">

        <!--logo and iconic logo start-->
        <div class="logo">
            <a href="<?php echo Wow::get("project/adminPrefix"); ?>"><img src="/assets/themes/adminex/images/logo.png" alt=""></a>
        </div>

        <div class="logo-icon text-center">
            <a href="<?php echo Wow::get("project/adminPrefix"); ?>"><img src="/assets/themes/adminex/images/logo_icon.png" alt=""></a>
        </div>
        <!--logo and iconic logo end-->

        <div class="left-side-inner">
            <!-- visible to small devices only -->
            <div class="visible-xs hidden-sm hidden-md hidden-lg">
                <div class="media logged-user">
                    <img alt="" src="/assets/images/avatar.jpg" class="media-object">
                    <div class="media-body">
                        <h4>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/account"><?php echo $uyelik["username"]; ?></a>
                        </h4>
                        <span><a href="<?php echo Wow::get("project/adminPrefix"); ?>/account/logout">Çıkış Yap</a></span>
                    </div>
                </div>
            </div>

            <!--sidebar nav start-->
            <ul class="nav nav-pills nav-stacked custom-nav">
                <li<?php if($controllerName == "Admin/Home") { ?> class="active"<?php } ?>>
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>"><i class="fa fa-home"></i>
                        <span>Anasayfa</span></a>
                </li>
                <li class="menu-list<?php if($controllerName == "Admin/Settings") { ?> nav-active<?php } ?>">
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/settings"><i class="fa fa-cog"></i>
                        <span>Ayarlar</span></a>
                    <ul class="sub-menu-list">
                        <li<?php if($controllerName == "Admin/Settings" && $actionName == "Index") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/settings"> Sistem Tanımları</a></li>
                        <li<?php if($controllerName == "Admin/Settings" && $actionName == "Cron") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/settings/cron"> Cronlar</a></li>
                    </ul>
                </li>
                <li class="menu-list<?php if($controllerName == "Admin/Islemler" || $controllerName == "Admin/Wizard") { ?> nav-active<?php } ?>">
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/islemler"><i class="fa fa-flash"></i>
                        <span>Araçlar</span></a>
                    <ul class="sub-menu-list">
                        <li<?php if($controllerName == "Admin/Islemler" && $actionName == "Index") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/islemler"> Pasif Kullanıcı Temizleme</a>
                        </li>
                        <li<?php if($controllerName == "Admin/Islemler" && $actionName == "CinsiyetTespit") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/islemler/cinsiyet-tespit"> Cinsiyet Tespiti</a>
                        </li>
                        <li<?php if($controllerName == "Admin/Islemler" && $actionName == "AddUserPass") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/islemler/add-user-pass"> User:Pass Aktarma</a>
                        </li>
                        <li<?php if($controllerName == "Admin/Islemler" && $actionName == "AddCookies") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/islemler/add-cookies"> Cookie Aktarma</a>
                        </li>
                        <li<?php if($controllerName == "Admin/Wizard" && $actionName == "Import") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/wizard/import"> İmport Data</a></li>
                        <li<?php if($controllerName == "Admin/Wizard" && $actionName == "Export") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/wizard/export"> Export Data</a></li>
                    </ul>
                </li>
                <li<?php if($controllerName == "Admin/Bakim") { ?> class="active"<?php } ?>>
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/bakim"><i class="fa fa-wrench"></i>
                        <span>Bakım</span></a>
                </li>
                <li class="menu-list<?php if($controllerName == "Admin/Insta") { ?> nav-active<?php } ?>">
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta"><i class="fa fa-instagram"></i>
                        <span>Instagram İşlemleri</span></a>
                    <ul class="sub-menu-list">
                        <li<?php if($controllerName == "Admin/Insta" && $actionName == "Index") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta"> Yeni İşlem</a></li>
                        <li<?php if($controllerName == "Admin/Insta" && $actionName == "AutoLikePackages") { ?>  class="active"<?php } ?>>
                            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta/auto-like-packages"> Oto Beğeni Paketleri</a>
                        </li>
                    </ul>
                </li>
                <?php if(Wow::has("project/plugins")) { ?>
                    <li class="menu-list<?php if(substr($controllerName, 0, strlen("Admin/Plugins")) == "Admin/Plugins") { ?> nav-active<?php } ?>">
                        <a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta"><i class="fa fa-gift"></i>
                            <span>Özel Eklentiler</span></a>
                        <ul class="sub-menu-list">
                            <?php foreach(Wow::get("project/plugins") as $k => $v) { ?>
                                <li<?php if($controllerName == "Admin/Plugins/" . $k) { ?>  class="active"<?php } ?>>
                                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/plugins/<?php echo $v["slug"]; ?>"> <?php echo $v["name"]; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
                <li<?php if($controllerName == "Admin/Uyeler") { ?> class="active"<?php } ?>>
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/uyeler"><i class="fa fa-users"></i>
                        <span>Üyeler</span></a>
                </li>
                <li<?php if($controllerName == "Admin/Sayfalar") { ?> class="active"<?php } ?>>
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/sayfalar"><i class="fa fa-file"></i>
                        <span>Sayfalar</span></a>
                </li>
                <li<?php if($controllerName == "Admin/Blog") { ?> class="active"<?php } ?>>
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/blog"><i class="fa fa-files-o"></i>
                        <span>Blog</span></a>
                </li>
                <li<?php if($controllerName == "Admin/Bayilik") { ?> class="active"<?php } ?>>
                    <a href="<?php echo Wow::get("project/adminPrefix"); ?>/bayilik"><i class="fa fa-thumbs-o-up"></i>
                        <span>Bayiler</span></a>
                </li>
            </ul>
            <!--sidebar nav end-->
        </div>
    </div>
    <div class="main-content">

        <div class="header-section">
            <a class="toggle-btn"><i class="fa fa-bars"></i></a>
            <form class="searchform" action="<?php echo Wow::get("project/adminPrefix"); ?>/insta" method="post">
                <input type="text" class="form-control" name="username" placeholder="Username Ara"/>
            </form>
            <div class="menu-right">
                <ul class="notification-menu">
                    <li>
                        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <img src="/assets/images/avatar.jpg" alt=""/>
                            <?php echo $uyelik["username"]; ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                            <li>
                                <a href="<?php echo Wow::get("project/adminPrefix"); ?>/account"><i class="fa fa-user"></i> Hesabım</a>
                            </li>
                            <li>
                                <a href="<?php echo Wow::get("project/adminPrefix"); ?>/account/logout"><i class="fa fa-sign-out"></i> Çıkış Yap</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!--notification menu end -->

        </div>
        <!-- header section end-->

        <!--body wrapper start-->
        <div class="wrapper">
            <section class="panel">
                <div class="panel-body">
                    <?php
                        if(count($this->get('notifications')) > 0) {
                            $this->renderView("shared/notifications", $this->get('notifications'));
                        }
                        $this->renderBody();
                    ?>
                </div>
            </section>
        </div>
        <!--body wrapper end-->

    </div>
    <!-- main content end-->
</section>
<?php $this->section("section_modals"); ?>
<?php $this->show(); ?>
<?php $this->section('section_scripts'); ?>
<!-- Placed js at the end of the document so the pages load faster -->
<script src="/assets/themes/adminex/js/jquery-1.10.2.min.js"></script>
<script src="/assets/themes/adminex/js/jquery-migrate-1.2.1.min.js"></script>
<script src="/assets/themes/adminex/js/bootstrap.min.js"></script>
<script src="/assets/themes/adminex/js/modernizr.min.js"></script>
<script src="/assets/themes/adminex/js/jquery.nicescroll.js"></script>
<script type="text/javascript" src="/assets/bootstrap-datepicker/js/bootstrap-datepicker.js?v=1"></script>
<!--common scripts for all pages-->
<script src="/assets/themes/adminex/js/scripts.js"></script>
<script type="text/javascript">
    function KeepSession() {
        $.ajax({type: 'GET', url: '<?php echo Wow::get("project/adminPrefix"); ?>/ajax/keep-session', dataType: 'json'});
        setTimeout(KeepSession, 5 * 60 * 1000);
    }

    setTimeout(KeepSession, 60 * 1000);
    <?php if(Wow::has("ayar/googleanalyticscode") != "") { ?>
    (function(i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function() {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src   = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
    ga('create', '<?php echo Wow::get("ayar/googleanalyticscode"); ?>', 'auto');
    ga('send', 'pageview');
    <?php } ?>
</script>
<?php $this->show(); ?>
</body>
</html>