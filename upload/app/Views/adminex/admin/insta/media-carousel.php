<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $item        = $model;
?>
<div class="entry-layout col-lg-4 col-md-4 col-sm-6 col-xs-12" id="entry<?php echo $item["id"]; ?>">
    <div class="entry-thumb transition">
        <div class="entry-media">
            <div class="carousel-area"><img src="/assets/images/carousel.png"></div>
            <div class="image">
                <a data-fancybox="gallery" href="<?php echo $item["carousel_media"][0]["media_type"]==2 ? str_replace("http:", "https:", $item["carousel_media"][$i]["video_versions"][0]["url"]) :  str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]); ?>">
                    <img class="img-responsive" src="<?php echo str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]); ?>"/>
                    <?php if($item["carousel_media"][0]["media_type"] == 2) { ?>
                        <div class="play-icon">
                            <?php echo intval($item["carousel_media"][0]["video_duration"]) . "sn"; ?>
                        </div>
                    <?php } ?>
                </a>
                <?php if(count($item["carousel_media"]) > 1) { ?>
                    <div style="display: none;">
                        <?php for($i = 1; $i < count($item["carousel_media"]); $i++) { ?>
                            <a data-fancybox="gallery"<?php if($item["carousel_media"][$i]["media_type"] == 1) { ?> href="<?php echo str_replace("http:", "https:", $item["carousel_media"][$i]["image_versions2"]["candidates"][0]["url"]); ?>"<?php } ?><?php if($item["carousel_media"][$i]["media_type"] == 2) { ?> href="#video<?php echo $item["id"].$i; ?>"<?php } ?>>
                                <?php echo $i; ?>
                            </a>
                            <?php if($item["carousel_media"][$i]["media_type"] == 2){ ?>
                                <div style="display: none;">
                                    <div id="video<?php echo $item["id"].$i; ?>">
                                        <video width="100%" controls>
                                            <source src="<?php echo str_replace("http:", "https:", $item["carousel_media"][$i]["image_versions2"]["candidates"][0]["url"]); ?>" type="video/mp4">
                                        </video>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php
                        } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="action-links">
            <div class="btn-group btn-group-justified">
                <a class="btn btn-default" href="#modalPopup" data-toggle="modal" onclick="sendPopup('<?php echo $item["id"]; ?>','send-like');"><i class="fa fa-heart"></i>
                    <span class="like_count"><?php echo $item["like_count"]; ?></span></a>
                <a class="btn btn-default" href="#modalPopup" data-toggle="modal" onclick="sendPopup('<?php echo $item["id"]; ?>','send-comment');"><i class="fa fa-comment"></i>
                    <span class="comment_count"><?php echo $item["comment_count"]; ?></span></a>
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-ellipsis-h"></i></button>
                    <ul class="dropdown-menu  dropdown-menu-right" role="menu">
                        <li>
                            <a href="#modalPopup" data-toggle="modal" onclick="sendPopup('<?php echo $item["id"]; ?>','send-like');">Beğeni Gönder</a>
                        </li>
                        <li>
                            <a href="#modalPopup" data-toggle="modal" onclick="sendPopup('<?php echo $item["id"]; ?>','send-comment');">Yorum Gönder</a>
                        </li>
                        <li>
                            <a download href="<?php echo isset($item["image_versions2"]) ? str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]) : ""; ?>">Resmi Kaydet</a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/p/<?php echo $item["code"]; ?>" target="_blank">Instagram'da Göster</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
