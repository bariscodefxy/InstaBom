<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $userInfo = $this->get("accountInfo")["user"];
    $this->set("title", "Insta: " . $userInfo["full_name"]);

    if($this->has('memberUrl')) { ?>
        <ul class="nav nav-tabs">
            <li class="active"><a href="javascript:void(0);">Instagram</a></li>
            <li><a href="<?php echo $this->get("memberUrl"); ?>">Üye</a></li>
        </ul>
    <?php } ?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-4">
                <a data-fancybox href="<?php echo isset($userInfo["hd_profile_pic_versions"]) ? str_replace("http:", "https:", end($userInfo["hd_profile_pic_versions"])["url"]) : str_replace("http:", "https:", $userInfo["profile_pic_url"]); ?>"><img class="img-responsive img-circle" id="profilePhoto" src="<?php echo str_replace("http:", "https:", $userInfo["profile_pic_url"]); ?>"/></a>
            </div>
            <div class="col-xs-8">
                <h4><?php echo $userInfo["full_name"]; ?>
                    <br/>
                    <small>@<?php echo $userInfo["username"]; ?></small>
                </h4>
                <p style="font-size: 13px;"><?php echo $userInfo["biography"]; ?></p>
                <?php if(!empty($userInfo["external_url"])) { ?><p style="font-size: 13px;">
                    <a href="<?php echo str_replace("http:", "https:", $userInfo["external_url"]); ?>" target="_blank"><?php echo str_replace("http:", "https:", strip_tags($userInfo["external_url"])); ?></a>
                    </p><?php } ?>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">İşlem Yap
                        <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#modalPopup" data-toggle="modal" onclick="sendPopup('<?php echo $userInfo["pk"]; ?>','send-follower');">Takipçi Gönder</a>
                        </li>
                        <?php if($userInfo["is_private"] != 1) { ?>
                            <li>
                                <a href="#modalPopup" data-toggle="modal" onclick="sendPopup('<?php echo $userInfo["pk"]; ?>','story-view');">Story Görüntülenme</a>
                            </li>
                            <li>
                                <a href="#modalAutoLikePackage" data-toggle="modal" onclick="addAutoLikePackage();">Oto Beğeni Ekle</a>
                            </li><?php } ?>
                        <li>
                            <a target="_blank" href="https://www.instagram.com/<?php echo $userInfo["username"]; ?>/">Instagramda Görüntüle</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="btn-group btn-group-justified" style="margin-top:5px;">
            <a class="btn btn-default"><?php echo $userInfo["media_count"]; ?> Gönderi</a>
            <a class="btn btn-default"><?php echo $userInfo["follower_count"]; ?> Takipçi</a>
            <a class="btn btn-default"><?php echo $userInfo["following_count"]; ?> Takip</a>
        </div>
    </div>
</div>
<?php
    if($userInfo["is_private"] == 1) { ?>
        <p class="text-danger">Uppps! Profil gizli. Gizli profillerin gönderilerine ulaşılamaz! Oto beğeni eklenemez!</p>
        <?php
    } else {
        $this->renderView("admin/instamis/list-media", $model);
    }
?>

<?php $this->section("section_head");
    $this->parent(); ?>
<link rel="stylesheet" href="/assets/fancybox/jquery.fancybox.min.css" type="text/css" media="screen"/>
<?php $this->endSection(); ?>

<?php $this->section("section_modals");
    $this->parent(); ?>
<div class="modal fade" id="modalPopup" style="z-index: 1051;">
    <div class="modal-dialog">
        <div class="modal-content" id="modalPopupInner">
        </div>
    </div>
</div>
<div class="modal fade" id="modalAutoLikePackage" style="z-index: 1051;">
    <div class="modal-dialog">
        <div class="modal-content" id="modalAutoLikePackageInner">
        </div>
    </div>
</div>
<?php if(!$this->has('memberUrl')) { ?>
    <div class="modal fade" id="modalAddMember" style="z-index: 1051;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="?formType=addMember">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Üye Olarak Ekle</h4>
                    </div>
                    <div class="modal-body">
                        <h3>@<?php echo $userInfo["username"]; ?></h3>
                        <div class="alert alert-info">
                            <p>Üyenin şifresi NA olarak kaydedilecektir. Üye sitede oturum açtığında şifre otomatik olarak güncellenir. Eğer üyeyi Kullanımda olarak kaydederseniz Pasif olabilir. Sürekli kalmasını istiyorsanız Kullanımda seçeneğini işaretlemeyin!
                            </p>
                        </div>
                        <input type="hidden" name="kullaniciAdi" value="<?php echo $userInfo["username"]; ?>">
                        <div class="form-group">
                            <label>Beğeni Kredisi</label>
                            <input type="number" name="begeniKredi" value="<?php echo Wow::get("ayar/yeniUyeBegeniKredi"); ?>" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Takipçi Kredisi</label>
                            <input type="number" name="takipKredi" value="<?php echo Wow::get("ayar/yeniUyeTakipKredi"); ?>" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Yorum Kredisi</label>
                            <input type="number" name="yorumKredi" value="<?php echo Wow::get("ayar/yeniUyeYorumKredi"); ?>" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kullanımda (Beğeni, Takip, Yorum yazdırılabilir.)</label>
                            <div class="checkbox">
                                <label><input type="checkbox" class="checkbox-inline" value="1" name="isUsable"> Kullanımda</label>
                            </div>
                            <span class="help-block">Bu kullanıcının beğeni, takip, yorum işlemlerinde kullanılmasını istemiyorsanız işareti kaldırın.</span>
                        </div>
                        <div class="form-group">
                            <label>Bayi</label>
                            <div class="checkbox">
                                <label><input type="checkbox" class="checkbox-inline" value="1" name="isBayi"> Light Bayi</label>
                            </div>
                            <span class="help-block">Bu kullanıcı kredilerini başkaları için de harcayabilsin istiyorsanız işaretleyin. Bu kısım işaretli olmadığında kredilerini sadece kendi gönderileri için kullanabilir.</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" onclick="$(this).html('Bekleyin..');">Üye Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
<?php $this->endSection(); ?>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script src="/assets/fancybox/jquery.fancybox.min.js"></script>
<script type="text/javascript">
    function loadMore(maxID) {
        $('#btnLoadMore').remove();
        $('#entry-list-row').first().append('<div class="tempLoading"><i class="fa fa-spinner fa-spin fa-2x"></i> Yüklüyor..</div>');
        $.ajax({url: '?formType=more', type: 'POST', data: 'maxid=' + maxID}).done(function(response) {
            $('#entry-list-row').append(response);
            $('.tempLoading').remove();
            $('a[data-fancybox]').fancybox();
        });
    }

    function sendPopup(mediaID, job) {
        $('#modalPopupInner').html('<div class="modal-body"><h2>Bekleyin..</h2></div>');
        $.ajax({url: '<?php echo Wow::get("project/adminPrefix"); ?>/instamis/' + job + '/' + mediaID, type: 'GET'}).done(function(data) {
            $('#modalPopupInner').html(data);
        });
    }

    function addAutoLikePackage() {
        $('#modalAutoLikePackageInner').html('<div class="modal-body"><h2>Bekleyin..</h2></div>');
        $.ajax({url: '<?php echo Wow::get("project/adminPrefix"); ?>/instamis/add-auto-like-package/<?php echo $userInfo["pk"]; ?>', type: 'GET'}).done(function(data) {
            $('#modalAutoLikePackageInner').html(data);
        });
    }

    $('a[data-fancybox]').fancybox();
</script>
<?php $this->endSection(); ?>
