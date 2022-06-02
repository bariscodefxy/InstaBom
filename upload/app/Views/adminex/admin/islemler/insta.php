<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "İnstagram İşlemleri")
    //TODO Instagram İşlemleri View ?>
<div class="panel panel-default">
    <div class="panel-heading">
        İnstagram İşlemleri
    </div>
    <div class="panel-body">
        <p>Bu bölümde, instagram kullanıcılarına beğeni, yorum ve takipçi gönderebileceksiniz. Bir sonraki güncellemeyi bekleyin.</p>
    </div>
</div>
<?php return; ?>
<?php
    $userInfo = $model["accountInfo"]["user"];
?>
<div class="panel panel-default">
    <div class="panel-heading">
        İnstagram İşlemleri
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-4">
                <a rel="fancyboxImage" href="<?php echo isset($userInfo["hd_profile_pic_versions"]) ? end($userInfo["hd_profile_pic_versions"])["url"] : str_replace("http:", "https:", $userInfo["profile_pic_url"]); ?>"><img class="img-responsive img-circle" id="profilePhoto" src="<?php echo str_replace("http:", "https:", $userInfo["profile_pic_url"]); ?>"/></a>
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
                <?php if(!empty($userInfo["profile_context"])) { ?>
                    <p class="text-primary" style="font-size:12px;"><?php echo $userInfo["profile_context"]; ?></p><?php } ?>
            </div>
        </div>
        <div class="btn-group btn-group-justified" style="margin-top:5px;">
            <button class="btn btn-default"><?php echo $userInfo["media_count"]; ?> Gönderi</button>
            <button class="btn btn-default"><?php echo $userInfo["follower_count"]; ?> Takipçi</button>
            <button class="btn btn-default"><?php echo $userInfo["following_count"]; ?> Takip</button>
        </div>
    </div>
</div>

<?php $this->section("section_scripts");
    $this->parent(); ?>

<?php $this->endSection(); ?>
