<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Mesaj Kutum");
?>
<?php if(empty($model["inbox"]["threads"])) { ?>
    <p class="text-danger">Mesaj kutunuz boş.</p>
<?php } else { ?>
    <div class="list-group message-list">
        <?php
            foreach($model["inbox"]["threads"] as $thread) {
                $user = $thread["users"][0]; ?>
                <a class="list-group-item" href="javascript:void(0);" onclick="getThreadDetails('<?php echo $thread["thread_id"]; ?>','<?php echo implode(",", array_map(function($deger) {
                    return $deger["pk"];
                }, $thread["users"])); ?>',true);">
                    <img class="img-circle" src="<?php echo str_replace("http:","https:",$user["profile_pic_url"]); ?>">
                    <div class="message-caption">
                        <strong><?php echo substr($thread["thread_title"], 0, 30); ?><?php echo strlen($thread["thread_title"]) > 30 ? '..' : ''; ?></strong>
                        <br/>
                        <?php $lastItem = $thread["items"][0];
                            switch($lastItem["item_type"]) {
                                case "text":
                                    echo $lastItem["text"];
                                    break;
                                case "like":
                                    echo '<i class="fa fa-heart text-danger"></i>';
                                    break;
                                case "media_share":
                                    if($lastItem["media_share"]["media_type"] == 1) {
                                        echo 'Fotoğraf paylaşıldı.';
                                    } else if($lastItem["media_share"]["media_type"] == 2) {
                                        echo 'Video paylaşıldı.';
                                    } else {
                                        echo $lastItem["item_type"];
                                    }
                                    break;
                                default:
                                    echo $lastItem["item_type"];
                            } ?>
                    </div>
                    <div class="clearfix"></div>
                </a>
            <?php } ?>
    </div>
<?php } ?>
