<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $logonPerson = $this->get("logonPerson");
    $uyelik      = $logonPerson->member;
    $accountInfo = $this->get("accountInfo");
    $item        = $model;

    if(isset($item["id"])) {
        ?>
        <div class="entry-layout col-lg-4 col-md-4 col-sm-6 col-xs-12" id="entry<?php echo $item["id"]; ?>">
            <div class="entry-thumb transition">
                <div class="entry-media">
                    <div class="carousel-area"><img src="/assets/images/carousel.png" style="position: absolute;top: -5px;left: -5px;"></div>
                    <?php if($accountInfo["user"]["pk"] != $item["user"]["pk"]) { ?>
                        <div class="text-absolute">
                            <a href="/user/<?php echo $item["user"]["pk"]; ?>"><img class="img-circle lazy" style="max-width:24px;" data-original="<?php echo str_replace("http:", "https:", $item["user"]["profile_pic_url"]); ?>"/>
                                <strong><?php echo $item["user"]["username"]; ?></strong></a>
                        </div>
                    <?php } ?>
                    <div class="image">
                        <a class="lightGalleryImage" id="lightGallery<?php echo $item["id"]; ?>0" data-sub-html='<div data-carousel-index="0" data-carousel-text="Medya: 1 / <?php echo count($item["carousel_media"]); ?>" class="fb-comments" data-id="<?php echo $item["id"]; ?>" id="comments<?php echo $item["id"]; ?>"><p class="text-center"><i class="fa fa-spinner fa-spin fa-4x active"></i></p></div>' <?php if($item["carousel_media"][0]["media_type"] == 1) { ?> href="<?php echo str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]); ?>"<?php } ?><?php if($item["carousel_media"][0]["media_type"] == 2) { ?> href="" data-poster="<?php echo str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]); ?>" data-html="#video<?php echo $item["id"] . "0"; ?>"<?php } ?>>
                            <img class="img-responsive lazy" data-original="<?php echo str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]); ?>"/>
                            <?php if($item["carousel_media"][0]["media_type"] == 2) { ?>
                                <div class="play-icon">
                                    <?php echo intval($item["carousel_media"][0]["video_duration"]) . "sn"; ?>
                                </div>
                            <?php } ?>
                        </a>
                        <?php if($item["carousel_media"][0]["media_type"] == 2) { ?>
                            <div style="display:none;" id="video<?php echo $item["id"] . "0"; ?>">
                                <video class="lg-video-object lg-html5" controls preload="none">
                                    <source src="<?php echo str_replace("http:", "https:", $item["carousel_media"][$i]["video_versions"][0]["url"]); ?>" type="video/mp4">
                                </video>
                            </div>
                        <?php } ?>
                        <?php if(count($item["carousel_media"]) > 1) { ?>
                            <div style="display: none;">
                                <?php for($i = 1; $i < count($item["carousel_media"]); $i++) { ?>
                                    <a class="lightGalleryImage" id="lightGallery<?php echo $item["id"] . $i; ?>" data-sub-html='<div data-carousel-index="<?php echo $i; ?>" data-carousel-id="<?php echo $item["id"]; ?> ?>" data-carousel-text="Medya: <?php echo $i + 1; ?> / <?php echo count($item["carousel_media"]); ?>" class="fb-comments" id="comments<?php echo $item["id"]; ?><?php echo $i; ?>" data-id="<?php echo $item["id"]; ?>"><h1 style="margin-top:0;">Carousel</h1><h6>Medya: <?php echo $i + 1; ?> / <?php echo count($item["carousel_media"]); ?></h6></div>' <?php if($item["carousel_media"][$i]["media_type"] == 1) { ?> href="<?php echo str_replace("http:", "https:", $item["carousel_media"][$i]["image_versions2"]["candidates"][0]["url"]); ?>"<?php } ?><?php if($item["carousel_media"][$i]["media_type"] == 2) { ?> href="" data-poster="<?php echo str_replace("http:", "https:", $item["carousel_media"][$i]["image_versions2"]["candidates"][0]["url"]); ?>" data-html="#video<?php echo $item["id"] . $i; ?>"<?php } ?>>
                                        <?php echo $i; ?>
                                    </a>
                                    <?php if($item["carousel_media"][$i]["media_type"] == 2) { ?>
                                        <div style="display:none;" id="video<?php echo $item["id"] . $i; ?>">
                                            <video class="lg-video-object lg-html5" controls preload="none">
                                                <source src="<?php echo str_replace("http:", "https:", $item["carousel_media"][$i]["video_versions"][0]["url"]); ?>" type="video/mp4">
                                            </video>
                                        </div>
                                    <?php }
                                } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="action-links">
                    <div class="btn-group btn-group-justified">
                        <a class="islemBtn btn btn-default" data-liked="<?php echo $item["has_liked"] == 1 ? '1' : '0'; ?>" onclick="like('<?php echo $item["id"]; ?>');" id="item<?php echo $item["id"]; ?>"><i class="fa fa-heart<?php echo $item["has_liked"] == 1 ? ' text-danger' : ''; ?>"></i>
                            <span class="like_count"><?php echo $item["like_count"]; ?></span></a>
                        <a class="btn btn-default" onclick="$('#lightGallery<?php echo $item["id"]; ?>').trigger('click')"><i class="fa fa-comment"></i>
                            <span class="comment_count"><?php echo $item["comment_count"]; ?></span></a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-ellipsis-h"></i></button>
                            <ul class="dropdown-menu  dropdown-menu-right" role="menu">
                                <li>
                                    <a download href="<?php echo isset($item["image_versions2"]) ? str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]) : ""; ?>">Resmi Kaydet</a>
                                </li>
                                <?php if($uyelik["instaID"] == $item["user"]["pk"]) { ?>
                                    <li>
                                        <a href="javascript:void(0);" onclick="deleteMedia('<?php echo $item["id"]; ?>');">Gönderiyi Sil</a>
                                    </li>
                                    <li>
                                        <a href="/tools/send-like/<?php echo $item["id"]; ?>">Beğeni Gönder</a>
                                    </li>
                                    <li>
                                        <a href="/tools/send-comment/<?php echo $item["id"]; ?>">Yorum Gönder</a>
                                    </li>
                                <?php } ?>
                                <?php if($uyelik->instaID != $item["user"]["pk"]) { ?>
                                    <li>
                                        <a href="javascript:void(0);" onclick="newMessage('<?php echo $item["user"]["pk"]; ?>');">Gönderi sahibine DM gönder</a>
                                    </li>
                                <?php } ?>
                                <li>
                                    <a href="javascript:void(0);" onclick="newMessage(null,'<?php echo $item["id"]; ?>');">Gönderiyi DM ile ilet</a>
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
    <?php } ?>