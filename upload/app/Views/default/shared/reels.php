<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $model
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $uyelik      = $logonPerson->member;
?>
<h4>Canlı Yayınlar</h4><div id="broadcastsList">
<?php if(empty($model["broadcasts"])){ ?>
    <p>Gösterilecek yayın yok!</p>
<?php } else { ?>
    <?php foreach($model["broadcasts"] as $list) { ?>
        <a href="#modalLiveVideo" data-toggle="modal" onclick="broadcastLiveVideo('<?php echo $list["dash_playback_url"] ?>');"><img class="img-circle pull-left" style="width: 50px;height:50px;margin:0 10px 10px 0;" src="<?php echo str_replace("http:", "https:", $list["broadcast_owner"]["profile_pic_url"]); ?>"/></a>
    <?php } ?>
    <div class="clearfix"></div>
<?php } ?>
</div>
<hr />
<h4>Hikayeler</h4>
<div id="reelsList">
<?php if(empty($model["tray"])){ ?>
    <p>Gösterilecek hikaye yok!</p>
<?php } else { ?>

    <?php
        foreach($model["tray"] as $list) {
        if(!isset($list["is_nux"]) && isset($list["items"]) && !empty($list["items"])) {
        foreach($list["items"] as $reel) {
        $isVideo  = isset($reel["video_versions"]);
        $mediaUrl = isset($reel["video_versions"]) ? $reel["video_versions"][0]["url"] : $reel["image_versions2"]["candidates"][0]["url"];
        $mediaUrl = str_replace("http:", "https:", $mediaUrl); ?><a class="lightGalleryImage" id="lightGallery<?php echo $reel["id"]; ?>" data-sub-html='<div class="fb-comments" id="comments<?php echo $reel["id"]; ?>"><div class="panel panel-primary"><div class="panel-body" style="word-wrap:break-word;"><img src="<?php echo str_replace("http:", "https:", $reel["user"]["profile_pic_url"]); ?>" class="img-circle" style="max-width: 26px;"><a href="/user/<?php echo $reel["user"]["pk"]; ?>"><strong><?php echo $reel["user"]["username"] ?></strong></a><?php if(isset($reel["caption"])) { ?><?php
        $desc = str_replace(array('"',"'"),array(" ", " "),$reel["caption"]["text"]);
        $desc = preg_replace('/#([^ \/][\w\._üÜğĞşŞıİçÇöÖ]+)/', '<a href="/account/tag/\1">#\1</a>', $desc);
        $desc = preg_replace('/@([^ \/][\w\._üÜğĞşŞıİçÇöÖ]+)/', '<a href="/user/\1">@\1</a>', $desc);
    ?><p><?php echo $desc; ?></p><?php } else { ?><p class="text-danger">Açıklama eklenmemiş!</p><?php } ?><p><i><?php echo date("d.m.Y H:i", $reel["caption"]["created_at"]); ?></i></p></div></div><?php if($reel["user"]["pk"] == $uyelik["instaID"]) { ?><a href="#modalEditMedia" class="btn btn-block btn-primary" data-toggle="modal" onclick="editMedia(&#39;<?php echo $reel["id"]; ?>&#39;);">Açıklama Düzenle</a><?php } ?></div>' <?php if(!$isVideo) { ?>href="<?php echo $mediaUrl; ?>"<?php } ?><?php if($isVideo) { ?> href="" data-poster="<?php echo str_replace("http:", "https:", $reel["image_versions2"]["candidates"][0]["url"]); ?>" data-html="#videoReel<?php echo $reel["id"]; ?>"<?php } ?>>
<img class="img-circle pull-left" style="width: 50px;height:50px;margin:0 10px 10px 0;" src="<?php echo str_replace("http:", "https:", $list["user"]["profile_pic_url"]); ?>"/></a>
<?php if($isVideo) { ?>
    <div style="display:none;" id="videoReel<?php echo $reel["id"]; ?>">
        <video class="lg-video-object lg-html5" controls preload="none">
            <source src="<?php echo str_replace("http:", "https:", $reel["video_versions"][0]["url"]); ?>" type="video/mp4">
        </video>
    </div>
<?php }}}} ?>
    <div class="clearfix"></div>
<?php } ?>
</div>



