<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->renderView("account/header");
?>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Profil Resmi
                </div>
                <div class="panel-body text-center">
                    <a rel="fancyboxImage" href="<?php echo isset($model["user"]["hd_profile_pic_versions"]) ? end($model["user"]["hd_profile_pic_versions"])["url"] : $model["user"]["profile_pic_url"]; ?>"><img class="img-circle" style="max-width:100%;" id="profilePhoto" src="<?php echo $model["user"]["profile_pic_url"]; ?>"/></a>
                    <div style="clear:both;"></div>
                    <div class="btn-group btn-group-xs" style="margin-top: 10px;">
                        <a class="btn btn-default" href="#modalChangeProfilePhoto" data-toggle="modal" onclick="changeProfilePhoto();">Değiştir</a>
                        <a class="btn btn-danger" href="javascript:void(0);" onclick="removeProfilePhoto();">Kaldır</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Gizlilik
                </div>
                <div class="panel-body">
                    <p>
                        <strong>Genele açık</strong> durumunda iken, tüm instagram kullanıcıları sizi takip edebilir ve paylaşımlarınızı görebilir.
                    </p>
                    <p>
                        <strong>Genele kapalı</strong> durumunda iken, sadece onayladığınız instagram kullanıcıları sizi takip edebilir ve paylaşımlarınızı görebilir.
                    </p>
                    <hr/>
                    <form method="post" action="?formType=accountPrivacy">
                        <?php if($model["user"]["is_private"] != 1) { ?>
                            <input type="hidden" name="do" value="private">
                            <p>Şu anda hesabınız <label class="label label-success">GENELE AÇIK</label> durumda.</p>
                            <p>
                                <button type="submit" class="btn btn-danger">Profilimi Genele Kapat</button>
                            </p>
                        <?php } else { ?>
                            <input type="hidden" name="do" value="public">
                            <p>Şu anda hesabınız <label class="label label-danger">GENELE KAPALI</label> durumda.</p>
                            <p>
                                <button type="submit" class="btn btn-success">Profilimi Genele Aç</button>
                            </p>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Hesap Bilgileri
        </div>
        <div class="panel-body">
            <form method="post" action="?formType=accountInformation">
                <div class="form-group">
                    <label>Ad Soyad</label>
                    <input class="form-control" type="text" name="first_name" value="<?php echo $model["user"]["full_name"]; ?>">
                </div>
                <div class="form-group">
                    <label>Cinsiyet</label>
                    <select class="form-control" name="gender">
                        <option value="0">Belirtilmemiş</option>
                        <option value="1"<?php echo $model["user"]["gender"] == 1 ? ' selected="selected"' : ''; ?>>Erkek</option>
                        <option value="2"<?php echo $model["user"]["gender"] == 2 ? ' selected="selected"' : ''; ?>>Bayan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="text" name="email" value="<?php echo $model["user"]["email"]; ?>">
                </div>
                <div class="form-group">
                    <label>Gsm</label>
                    <input class="form-control" type="text" name="phone_number" value="<?php echo $model["user"]["phone_number"]; ?>">
                </div>
                <div class="form-group">
                    <label>İnternet Sitesi</label>
                    <input class="form-control" type="text" name="external_url" value="<?php echo $model["user"]["external_url"]; ?>">
                </div>
                <div class="form-group">
                    <label>Biyografi</label>
                    <textarea name="biography" class="form-control" style="height: 100px;"><?php echo $model["user"]["biography"]; ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>