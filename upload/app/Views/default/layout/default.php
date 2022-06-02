<?php
    /**
     * Wow Master Template
     *
     * @var \Wow\Template\View      $this
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $uyelik      = $logonPerson->member;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php $this->section("section_head"); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap-paper.min.css">
    <link type="text/css" rel="stylesheet" href="/assets/lightgallery/dist/css/lightgallery.min.css"/>
    <link rel="stylesheet" href="/assets/scripts/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen"/>
    <link rel="stylesheet" href="/assets/style/font-awesome.min.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="/assets/style/paper.css?v=v3.1.0">
    <link rel="stylesheet" href="/assets/nprogress/nprogress.css">
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
<body <?php if(isset($_SESSION["instamisToken"])) { ?>style="padding-top:0 !important;"<?php } ?>>
<?php if(!isset($_SESSION["instamisToken"])) { ?>
    <header>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header pull-left">
                    <a class="navbar-brand" href="<?php echo $logonPerson->isLoggedIn() ? '/tools' : '/'; ?>"><img alt="instagram takip" src="/assets/images/logo.png"/></a>
                </div>
                <div class="navbar-header pull-right">
                    <ul class="nav navbar-nav pull-left">
                        <?php if(!$logonPerson->isLoggedIn()) { ?>
                            <li><p class="navbar-btn">
                                    <a id="loginAsUser" class="btn btn-primary" href="<?php echo Wow::get("project/memberLoginPrefix"); ?>"><i class="fa fa-sign-in"></i> GİRİŞ</a>
                                </p></li>
                        <?php } else { ?>
                            <li class="dropdown pull-right">
                                <a href="" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="text-transform: none;">
                                    <img src="<?php echo str_replace("http:", "https:", $uyelik["profilFoto"]); ?>" alt="<?php $uyelik["kullaniciAdi"]; ?>" style="max-height:30px;"> <?php echo (strlen($uyelik["fullName"]) > 10) ? substr($uyelik["fullName"], 0, 5) . ".." : $uyelik["fullName"]; ?>
                                    <span class="caret"></span></a>
                                <ul role="menu" class="dropdown-menu dropdown-light fadeInUpShort">
                                    <?php if($logonPerson->member->isBayi == 1) { ?>
                                        <li><a href="/bayi-tx" class="menu-toggler"> Bayi Paneli </a></li>
                                        <li class="divider"></li>
                                    <?php } ?>
                                    <li>
                                        <a href="/user/<?php echo $logonPerson->member->instaID; ?>" class="menu-toggler"> Profilim </a>
                                    </li>
                                    <li><a href="/account/settings" class="menu-toggler"> Hesap Ayarları </a></li>
                                    <li class="divider"></li>
                                    <li><a href="/account/logout" class="menu-toggler"> Çıkış Yap </a></li>
                                </ul>
                            </li>
                            <li class="pull-right">
                                <a href="/messages" title="Mesaj Kutum" style="text-transform: none;">
                                    <img src="/assets/images/direct_icon.png" style="max-height:30px;">
                                    <span class="badge<?php echo isset($_SESSION["NonReadThreadCount"]) && intval($_SESSION["NonReadThreadCount"]) > 0 ? '' : ' hidden'; ?>" id="nonReadThreadCount"><?php echo isset($_SESSION["NonReadThreadCount"]) ? $_SESSION["NonReadThreadCount"] : 0; ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>


                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse navbar-left">
                    <ul class="nav navbar-nav">
                        <li<?php echo $this->route->params["controller"] == "Tools" ? ' class="active"' : ''; ?>>
                            <a href="/tools"><?php echo $this->translate("instagram/menu/tools"); ?></a></li>
                        <li<?php echo $this->route->params["controller"] == "Packages" ? ' class="active"' : ''; ?>>
                            <a href="/packages"><?php echo $this->translate("instagram/menu/packages"); ?></a></li>
                        <?php if(!$logonPerson->isLoggedIn()) { ?>
                        <li<?php echo $this->route->params["controller"] == "Blog" ? ' class="active"' : ''; ?>>
                                <a href="/blog">Blog</a></li><?php } ?>
                    </ul>
                </div>
                <?php if($logonPerson->isLoggedIn()) { ?>
                    <div class="navbar-collapse collapse navbar-right">
                        <?php if($logonPerson->isLoggedIn()) { ?>
                            <div class="pull-left">
                                <form class="navbar-form" role="search" action="/account/search">
                                    <input type="hidden" name="tab" value="<?php echo $this->route->params["controller"] == "Account" && ($this->route->params["action"] == "Search" || $this->route->params["action"] == "Tag" || $this->route->params["action"] == "Location") ? $this->get("tab") : ""; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Ara" name="q" value="<?php echo $this->route->params["controller"] == "Account" && ($this->route->params["action"] == "Search" || $this->route->params["action"] == "Tag" || $this->route->params["action"] == "Location") ? $this->get("q") : ""; ?>" required>
                                        <div class="input-group-btn">
                                            <button class="btn btn-default" type="submit">
                                                <i class="glyphicon glyphicon-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </nav>
    </header>
<?php } ?>
<?php
    if($logonPerson->isLoggedIn()) {
        $this->renderView("shared/account-bar");
    }
    if($this->has("navigation")) {
        $this->renderView("shared/navigation", $this->get("navigation"));
    }
    if(count($this->get('notifications')) > 0) {
        $this->renderView("shared/notifications", $this->get('notifications'));
    }
    $this->renderBody();
?>
<footer class="bg-dark footer-one">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <center><img alt="site logo" class="img-responsive" src="/assets/images/logo.png"/>
                <p>
                    <a href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME']; ?>">Instagram Takipçi ve Beğeni Hilesi</a>
                </p>
                <p>Copyright &copy; <?php echo date("Y"); ?>
                    <a href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http') . "://" . $_SERVER['SERVER_NAME']; ?>"><?php echo $_SERVER['SERVER_NAME']; ?></a></center><br>
                </p>
            </div>
            <div class="col-md-3">
                <h5>Nasıl Çalışır</h5>
                <p>Kredileriniz ile dilediğiniz paylaşımınıza beğeni ve profilinize takipçi gönderebilirsiniz.
                    <a href="/packages">Paketler</a> bölümünden uygun fiyatlar ile bir paket satın alabilirsiniz.</p>
            </div>
            <div class="col-md-3">
                <h5>Kimler Kullanabilir</h5>
                <p>Instagram üyeliği olan herkes sistemi kullanabilir. Instagram hesabınızla giriş yapın ve hemen kullanmaya başlayın. Kullanım ücretsizdir. Kredi satın almadıkça hiçbir ücret ödemezsiniz.</p>
            </div>
            <div class="col-md-3">
                <h5>Bize Ulaşın</h5>
<?php if(Wow::get("ayar/contact_whatsapp") != "") { ?>
                <class="font-secondary font-15"><a style="color: #00ff00;"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp:</a> <?php echo Wow::get("ayar/contact_whatsapp"); ?><br>
<?php } ?>
<?php if(Wow::get("ayar/contact_instagram") != "") { ?>
                        <face="tahoma""><a style="color: #dd2a7b;"><i class="fa fa-instagram" aria-hidden="true"></i> Instagram:</a> <?php echo Wow::get("ayar/contact_instagram"); ?><br>
<?php } ?>
<?php if(Wow::get("ayar/contact_mail") != "") { ?>
                <class="font-secondary font-15"><a style="color: #ffbe00;"><i class="fa fa-envelope-o" aria-hidden="true"></i> E-Posta:</a> <?php echo Wow::get("ayar/contact_mail"); ?><br>
<?php } ?>
<?php if(Wow::get("ayar/contact_skype") != "") { ?>
                        <face="tahoma""><a style="color: #00aff0;"><i class="fa fa-skype" aria-hidden="true"></i> Skype:</a> <?php echo Wow::get("ayar/contact_skype"); ?><br>
<?php } ?>
<a href="https://instaturk.org/" title="smm panel">smm panel</a>
<a href="https://ig.web.tr/" title="smm panel">instagram takipçi hilesi</a>
            </div>
        </div>
    </div>
</footer>
<?php $this->section("section_modals");
    if($logonPerson->isLoggedIn()) { ?>
        <div class="modal" id="modalEditMedia" style="z-index: 1051;">
            <div class="modal-dialog">
                <div class="modal-content" id="modalEditMediaInner">
                </div>
            </div>
        </div>
        <div class="modal" id="modalChangeProfilePhoto" style="z-index: 1051;">
            <div class="modal-dialog">
                <div class="modal-content" id="modalChangeProfilePhotoInner">
                </div>
            </div>
        </div>
        <div class="modal fade" id="addPicture">
            <div class="modal-dialog">
                <div class="modal-content">
                    <ul class="nav nav-tabs nav-justified nav-tabs-justified">
                        <li class="active"><a href="#addByUploadImage" data-toggle="tab">Resim Yükle</a></li>
                        <li><a href="#addByUrl" data-toggle="tab">Url'den Ekle</a></li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade active in" id="addByUploadImage">
                            <form method="post" action="/account/upload-image" enctype="multipart/form-data" onsubmit="$('#btnUploadImage').html('YÜKLENİYOR BEKLEYİN..').removeClass('btn-primary').addClass('btn-success').attr('disabled','disabled');">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Resim Yükle</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Açıklama</label>
                                        <input class="form-control" type="text" name="aciklama" placeholder="Ne düşünüyorsun..">
                                    </div>
                                    <div class="form-group">
                                        <label>Resim</label>
                                        <input type="file" name="file" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                                    <button type="submit" id="btnUploadImage" class="btn btn-primary">Kaydet</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="addByUrl">
                            <form method="post" action="/account/upload-image-from-url" onsubmit="$('#btnAddImage').html('YÜKLENİYOR BEKLEYİN..').removeClass('btn-primary').addClass('btn-success').attr('disabled','disabled');">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">Url İle Resim Ekle</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Açıklama</label>
                                        <input class="form-control" type="text" name="aciklama" placeholder="Ne düşünüyorsun..">
                                    </div>
                                    <div class="form-group">
                                        <label>Resim Url</label>
                                        <input type="text" name="url" class="form-control" placeholder="Örn: http://site.com/image.jpg" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                                    <button type="submit" class="btn btn-primary" id="btnAddImage">Kaydet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalNewMessage">
            <div class="modal-dialog">
                <div class="modal-content" id="modalNewMessageInner">
                </div>
            </div>
        </div>
    <?php } ?>
<div class="modal fade" id="modalContact" style="z-index: 1051;">
    <div class="modal-dialog">
        <div class="modal-content" id="infoModal">
            <div class="modal-body">
                <h3>İletişim Bilgileri</h3>
                <p>Kredi satın almak için aşağıda bulunan iletişim kanallarından bize ulaşabilirsiniz.</p>
                <?php if(Wow::get("ayar/contact_whatsapp") != "") { ?>
                    <p><span style="color:#43d854"><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp </span>:
                        <b><?php echo Wow::get("ayar/contact_whatsapp"); ?></b></p>
                <?php } ?>
                <?php if(Wow::has("ayar/contact_skype") != "") { ?>
                    <p><span style="color:#00aff0"><i class="fa fa-skype" aria-hidden="true"></i> Skype </span> :
                        <b><?php echo Wow::get("ayar/contact_skype"); ?></b></p><br>
                        <a href="https://instaturk.org/" title="smm panel">smm panel</a>
                        <a href="https://ig.web.tr/" title="smm panel">instagram takipçi hilesi</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalSorumluluk" style="z-index: 1051;">
<div class="modal-dialog">
<div class="modal-content" id="modalSorumlulukInner">
<div class="modal-header"><button aria-hidden="true" class="close" data-dismiss="modal" type="button">×</button>

<h4 class="modal-title"><?php echo Wow::get("ayar/site_title"); ?> Sorumluluk Kuralları</h4>
</div>

<div class="modal-body">
<div class="well">
<p>Her birey kendisinden sorumludur site üzerinden yapılan hiçbir işlem web site sahibi ile alakası olmamaktadır. Bunun yanı sıra mahkemede tespit edilip işlemi yapan kullanıcı bilgileri mahkemeye sunularak tarafımız sorumluluğunu getirecektir.</p>

<p>Kullanıcı Adı, şifre veya e-posta değişimlerinden hiçbir türlü web sitemiz sorumlu değildir. Kullanıcı Sisteme giriş yaptığında tamamen kendi sorumluluğu altındadır.</p>

<p>Web site üzerinde yapılan spam işlemler mahkemeye sunulacaktır.</p>

<p>Web Sitemiz Kullanıcı Havuzu yöntemiyle çalışır. Sizler işlem yaparken başka kişiler hesabınızdan işlem talep ediyor olabilir.</p>

<p>Web siteye yapılan kötülemeler ve iftiralar sonucu bu kötülemeleri ve iftiraları yapan kişi mahkemeye verilecektir.</p>

<p>Instagram şifreleri otomatik sıfırlanmaktadır. Bu işlem gerçekleştiğinde web sitemiz sorumlu değildir.</p>

<p>Instagram hesabınızın kapatılması spam olması veya askıya alınması sitemiz tarafından sorumlu tutulmamaktadır.</p>

<p><?php echo Wow::get("ayar/site_title"); ?> instagram ile giriş yapan kullanıcıların herhangi bir bilgileri sitemizde barındırmamak üzere 24 saatte bir silinmektedir.</p>

<p>Hiçbir kullanıcı geçmiş işlemler için kayıt listesine ulaşamayacaktır.</p>

<p>Değişen kullanıcı adı, şifre ve e-postalardan sitemiz sorumlu olmamakla beraber kullanıcı hiçbir hak talebinde bulunamayacaktır.</p>

<p>Web sitemizi gereksiz yere mahkeme ve benzeri işlemler ile rahatsız edenler kişilere tazminat davası açılacaktır.</p>

<p>Sisteme girmiş tüm kullanıcılar bu maddeleri okumuş ve kabul etmiş sayılmaktadır.</p>
</div>
</div>
</div>
</div>
</div>

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
<?php $this->show(); ?>
<script type="text/javascript">
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
    initProject();
</script>

<style>
    /* The Modal (background) */
    .modal {
        display          : none; /* Hidden by default */
        position         : fixed; /* Stay in place */
        z-index          : 999999; /* Sit on top */
        padding-top      : 100px; /* Location of the box */
        left             : 0;
        top              : 0;
        width            : 100%; /* Full width */
        height           : 100%; /* Full height */
        overflow         : auto; /* Enable scroll if needed */
        background-color : rgb(0, 0, 0); /* Fallback color */
        background-color : rgba(0, 0, 0, 0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color : #fefefe;
        margin           : auto;
        padding          : 20px;
        border           : 1px solid #888;
        width            : 80%;
    }

    /* The Close Button */
    .close {
        color       : #aaaaaa;
        float       : right;
        font-size   : 28px;
        font-weight : bold;
    }

    .close:hover,
    .close:focus {
        color           : #000;
        text-decoration : none;
        cursor          : pointer;
    }
</style>

<!-- The Modal -->
<!--<div id="myModal" class="modal">-->
<!---->
<!--    <!-- Modal content -->
<!--    <div class="modal-content">-->
<!--        <span class="close">&times;</span>-->
<!--        <p style="text-align:center;">-->
<!--            <img style="width:100px;" src="http://insta.web.tr/assets/icon.png"/><br/>-->
<!--        <div style="font-size:22px;text-align:center;">Instamis uygulamasını indirerek her gün-->
<!--            <b>1000</b> takipçi, beğeni, yorum ve story görüntülenmesi kazanabilirsiniz.<br/>-->
<!--            <a target="_blank" href="https://play.google.com/store/apps/details?id=org.instamoda">https://play.google.com/store/apps/details?id=org.instamoda</a><br/>-->
<!--        </div>-->
<!--        </p>-->
<!--    </div>-->
<!--</div>-->

<?php $this->section('section_scripts');
    $this->parent(); ?>
<script>
    // // Get the modal
    // var modal = document.getElementById('myModal');
    //
    // // Get the <span> element that closes the modal
    // var span = document.getElementsByClassName("close")[0];
    //
    // // When the user clicks on <span> (x), close the modal
    // span.onclick = function() {
    //     modal.style.display = "none";
    // }
    //
    // // When the user clicks anywhere outside of the modal, close it
    // window.onclick = function(event) {
    //     if(event.target == modal) {
    //         modal.style.display = "none";
    //     }
    // }
    //
    // document.addEventListener('DOMContentLoaded', function() {
    //     modal.style.display = "block";
    // }, false);
</script>
<?php $this->show(); ?>
</body>
</html>