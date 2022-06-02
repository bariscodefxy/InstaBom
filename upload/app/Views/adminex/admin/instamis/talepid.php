<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $talep = $model;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><?php echo isset($talep["userData"]["user"]["username"]) ? "@" . $talep["userData"]["user"]["username"] . " - " : ""; ?>Talep Detayları</h4>
</div>

<div class="modal-body">
    <?php if($talep["talepTip"] == "story" || $talep["talepTip"] == "takip") {
        if(empty($talep["userData"]["user"]["username"])) {
            echo "Üyelik Bilgileri Alınamadı";
        } else {
            ?>
            <div class="profile" style="text-align:center;">
                <img src="<?php echo $talep["userData"]["user"]["profile_pic_url"]; ?>" style="border-radius:50%;margin:10px;    box-shadow: 0 1px 2px 1px #808080;"/>
                <div class="profile-info">
                    <div class="full-name" style="font-size: 18px;font-weight:bold;"><?php echo $talep["userData"]["user"]["full_name"]; ?></div>
                    <div class="user-name">@<?php echo $talep["userData"]["user"]["username"]; ?></div>
                    <div class="profile-count">
                        <b><?php echo $talep["userData"]["user"]["media_count"]; ?></b> Media /
                        <b><?php echo $talep["userData"]["user"]["following_count"]; ?></b> Takip Ettiği /
                        <b><?php echo $talep["userData"]["user"]["follower_count"]; ?></b> Takipçi
                    </div>
                </div>
            </div>
        <?php }
    } else { ?>
        <div class="media-data" style="text-align:center;">
            <div style="width: 300px;margin: 0 auto;text-align: left;font-weight: bold;margin-bottom: 5px;padding-bottom: 5px;border-bottom: 1px solid #d6d6d6;"><a style="text-decoration:none;color:#7A7676" href="https://instagram.com/<?php echo $talep["mediaData"]["items"][0]["user"]["username"]; ?>" target="_blank"><img style="width:25px;height:25px;border-radius:50%;vertical-align: -7px;margin-right: 5px;" src="<?php echo $talep["mediaData"]["items"][0]["user"]["profile_pic_url"]; ?>"/><?php echo $talep["mediaData"]["items"][0]["user"]["username"]; ?></a>
                </div>
            <img src="<?php echo $talep["mediaData"]["items"][0]["image_versions2"]["candidates"][0]["url"]; ?>" style="width:300px;"/>
            <div style="width: 300px;margin: 0 auto;text-align: left;font-weight: bold;margin-bottom: 5px;padding-bottom: 5px;border-bottom: 1px solid #d6d6d6;margin-top: 7px;">
                <i style="color:#ff1522" class="fa fa-heart"></i> <?php echo $talep["mediaData"]["items"][0]["like_count"]; ?> Beğeni
            </div>
            <?php if(!empty($talep["mediaData"]["items"][0]["caption"]["text"])) { ?>
                <div style="text-align:center"><?php echo substr($talep["mediaData"]["items"][0]["caption"]["text"],0,300); ?><?php if(strlen($talep["mediaData"]["items"][0]["caption"]["text"]) > 300) { ?>...<?php } ?></div>
            <?php } ?>
            <div style="font-size: 18px;color: #b1b1b1;font-weight: bold;border-top: 1px solid #dadada;padding-top: 15px;margin-top: 15px;"><?php echo $talep["mediaData"]["items"][0]["comment_count"] > 0 ? $talep["mediaData"]["items"][0]["comment_count"] . " Adet Yorum" : ""; ?></div>
        </div>
    <?php } ?>
</div>
<?php if((($talep["talepTip"] == "story" || $talep["talepTip"] == "takip") && !isset($talep["userData"]["user"]["username"])) && (($talep["talepTip"] == "yorum" || $talep["talepTip"] == "begeni") && !isset($talep["mediaData"]["items"][0]["code"]))) { ?>
    <div class="modal-footer">
        <button data-dismiss="modal" class="btn btn-primary" type="button">Kapat</button>
    </div>
<?php } else { ?>
    <div class="modal-footer">
        <?php if($talep["talepTip"] == "story" || $talep["talepTip"] == "takip") { ?>
            <a href="https://instagram.com/<?php echo $talep["userData"]["user"]["username"]; ?>" target="_blank" class="btn btn-primary" type="button">Profili Instagram'da İncele</a>
        <?php } else { ?>
            <a href="https://www.instagram.com/p/<?php echo $talep["mediaData"]["items"][0]["code"]; ?>/" target="_blank" class="btn btn-primary" type="button">Gönderiyi Instagram'da İncele</a>
        <?php } ?>

    </div>
<?php } ?>
