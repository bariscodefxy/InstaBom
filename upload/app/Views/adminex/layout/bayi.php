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
    $controllerName = explode("/", $this->route->params["controller"])[1];
    $actionName     = $this->route->params["action"];
    $helpers        = new \App\Libraries\Helpers();
    $bugun          = date("Y-m-d");
    $sonaErme       = date("Y-m-d", strtotime($logonPerson->member->sonaErmeTarihi));
    $remainingDay   = $helpers->tarihFark($sonaErme, $bugun, '-');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php $this->section("section_head"); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/style/font-awesome.min.css" type="text/css" media="screen"/>
    <link href="/assets/themes/adminex/css/style.css?v=3" rel="stylesheet">
    <link href="/assets/themes/adminex/css/style-responsive.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/assets/bootstrap-datepicker/css/datepicker-custom.css"/>
    <link rel="shortcut icon" href="/assets/images/favicon.png" type="image/x-icon"/>
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
            <a href="<?php echo Wow::get("project/resellerPrefix"); ?>"><img src="/assets/themes/adminex/images/logo.png" alt=""></a>
        </div>

        <div class="logo-icon text-center">
            <a href="<?php echo Wow::get("project/resellerPrefix"); ?>"><img src="/assets/themes/adminex/images/logo_icon.png" alt=""></a>
        </div>
        <!--logo and iconic logo end-->

        <div class="left-side-inner">
            <!-- visible to small devices only -->
            <div class="visible-xs hidden-sm hidden-md hidden-lg">
                <div class="media logged-user">
                    <img alt="" src="/assets/images/avatar.jpg" class="media-object">
                    <div class="media-body">
                        <h4><?php echo $uyelik["username"]; ?></h4>
                        <span><a href="<?php echo Wow::get("project/resellerPrefix"); ?>/account/logout">Çıkış Yap</a></span>
                    </div>
                </div>
            </div>

            <!--sidebar nav start-->
            <ul class="nav nav-pills nav-stacked custom-nav">
                <li<?php if($controllerName == "Home" && $actionName == "Index") { ?> class="active"<?php } ?>>
                    <a href="<?php echo Wow::get("project/resellerPrefix"); ?>"><i class="fa fa-home"></i>
                        <span>Anasayfa</span> <?php if($uyelik["smmActive"] == "aktif") { ?>
                            <span class="badge"><?php echo $uyelik["bakiye"] . " ₺" ?></span><?php } ?></a>
                </li>


                <?php

                    $bulkTasks = array();

                    if($uyelik["smmActive"] == "pasif") {
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-like",
                            "text"           => "Beğeni Gönder",
                            "action"         => "SendLike",
                            "icon"           => "fa fa-heart text-success",
                            "limitterColumn" => "gunlukBegeniLimitLeft"
                        );
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-reel-like",
                            "text"           => "Reel Beğeni Gönder",
                            "action"         => "SendReelLike",
                            "icon"           => "fa fa-heart text-danger",
                            "limitterColumn" => "gunlukBegeniLimitLeft"
                        );
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-follower",
                            "text"           => "Takipçi Gönder",
                            "action"         => "SendFollower",
                            "icon"           => "fa fa-user-plus text-info",
                            "limitterColumn" => "gunlukTakipLimitLeft"
                        );
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-comment",
                            "text"           => "Yorum Gönder",
                            "action"         => "SendComment",
                            "icon"           => "fa fa-comment",
                            "limitterColumn" => "gunlukYorumLimitLeft"
                        );
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-comment-like",
                            "text"           => "Yorum Beğeni",
                            "action"         => "SendCommentLike",
                            "icon"           => "fa fa-heart",
                            "limitterColumn" => "gunlukYorumBegeniLimitLeft"
                        );
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-video-view",
                            "text"           => "Video Görüntülenme",
                            "action"         => "SendVideoView",
                            "icon"           => "fa fa-video-camera",
                            "limitterColumn" => "gunlukVideoLimitLeft"
                        );
                         $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-igtv-video",
                            "text"           => "İg TV Görüntülenme",
                            "action"         => "SendigtvVideo",
                            "icon"           => "fa fa-instagram text-warning",
                            "limitterColumn" => "gunlukVideoLimitLeft"
                        );
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-reel-video",
                            "text"           => "Reel Görüntülenme",
                            "action"         => "SendreelVideo",
                            "icon"           => "fa fa-play-circle text-success",
                            "limitterColumn" => "gunlukVideoLimitLeft"
                        );
                        
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-canli-yayin-view",
                            "text"           => "Canlı Yayın izlenme",
                            "action"         => "SendCanliYayin",
                            "icon"           => "fa fa-heartbeat text-success",
                            "limitterColumn" => "gunlukCanliYayinLimitLeft"
                        );
                                  $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-canli-yayin-like",
                            "text"           => "Canlı Yayın Beğeni",
                            "action"         => "SendCanliYayin",
                            "icon"           => "fa fa-heart",
                            "limitterColumn" => "gunlukCanliYayinLimitLeft"
                        );
                     $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-canli-yayin-comment",
                            "text"           => "Canlı Yayın Yorum",
                            "action"         => "SendCanliComment",
                            "icon"           => "fa fa-heart",
                            "limitterColumn" => "gunlukCanliYayinLimitLeft"
                        );
                       $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-save",
                            "text"           => "Kaydetme",
                            "action"         => "SendSave",
                            "icon"           => "fa fa-save",
                            "limitterColumn" => "gunlukSaveLimitLeft"
                        );
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/send-story",
                            "text"           => "Story Gönder",
                            "action"         => "SendStory",
                            "icon"           => "fa fa-instagram",
                            "limitterColumn" => "gunlukStoryLimitLeft"
                        );
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/add-auto-like-package",
                            "text"           => "Oto Beğeni Ekle",
                            "action"         => "AddAutoLikePackage",
                            "icon"           => "fa fa-heartbeat",
                            "limitterColumn" => "toplamOtoBegeniLimitLeft"
                        );
                    } else {
                        $bulkTasks[] = array(
                            "link"           => Wow::get("project/resellerPrefix") . "/home/talepler",
                            "text"           => "Talepler",
                            "action"         => "Talepler",
                            "icon"           => "fa fa-plug",
                            "limitterColumn" => "bakiye"
                        );
                    }

                ?>
                <?php foreach($bulkTasks as $menu) { ?>
                    <li<?php if($controllerName == "Home" && $actionName == $menu["action"]) { ?> class="active"<?php } ?>>
                        <a href="<?php echo $menu["link"]; ?>">
                            <i class="<?php echo $menu["icon"]; ?>"></i> <span><?php echo $menu["text"]; ?>
                                <span class="badge"><?php echo isset($logonPerson->member[$menu["limitterColumn"]]) ? $logonPerson->member[$menu["limitterColumn"]] : ""; ?></span></span>
                        </a></li>
                <?php } ?>
                <li<?php if($controllerName == "Home" && $actionName == "ApiDocs") { ?> class="active"<?php } ?>>
                    <a href="<?php echo Wow::get("project/resellerPrefix"); ?>/home/api-docs">
                        <i class="fa fa-code"></i> <span>Api Dökümanı</span>
                    </a></li>
                <?php if($uyelik["smmActive"] == "pasif") { ?>
                    <li<?php if($controllerName == "Home" && $actionName == "List") { ?> class="active"<?php } ?>>
                        <a href="<?php echo Wow::get("project/resellerPrefix"); ?>/home/list">
                            <i class="fa fa-list"></i> <span>İşlem Geçmişi</span>
                        </a></li>
                <?php } ?>


            </ul>
            <!--sidebar nav end-->
        </div>
    </div>
    <!-- left side end-->
    <!-- main content start-->
    <div class="main-content">

        <!-- header section start-->
        <div class="header-section">

            <!--toggle button start-->
            <a class="toggle-btn"><i class="fa fa-bars"></i></a>
            <!--toggle button end-->

            <!--search start-->
            <form class="searchform" action="<?php echo Wow::get("project/resellerPrefix"); ?>/home/list" method="get">
                <input type="text" class="form-control" name="q" placeholder="İşlem Ara: Username"/>
            </form>
            <!--search end-->

            <!--notification menu start -->
            <div class="menu-right">
                <ul class="notification-menu">
                    <li class="remainingday">Mevcut Bakiye : <?php echo $uyelik["bakiye"]; ?> ₺</li>
                    <li class="remainingday">Kalan Gün : <?php echo $remainingDay; ?></li>
                    <li>
                        <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <img src="/assets/images/avatar.jpg" alt=""/>
                            <?php echo $uyelik["username"]; ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                            <li>
                                <a href="<?php echo Wow::get("project/resellerPrefix"); ?>/account/logout"><i class="fa fa-sign-out"></i> Çıkış Yap</a>
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
<script src="/assets/jquery/2.2.4/jquery.min.js"></script>
<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets/scripts/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="/assets/lightgallery/dist/js/lightgallery.min.js"></script>
<script src="/assets/lightgallery/dist/js/lg-video.min.js"></script>
<script src="/assets/lazyload/jquery.lazyload.min.js"></script>
<script src="/assets/nprogress/nprogress.js"></script>
<script src="/assets/core/core.js?v=3.1.10"></script>
<script type="text/javascript">
    function KeepSession() {
        $.ajax({type: 'GET', url: '<?php echo Wow::get("project/resellerPrefix"); ?>/ajax/keep-session', dataType: 'json'});
        setTimeout(KeepSession, 30 * 1000);
    }

    setTimeout(KeepSession, 30 * 1000);
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