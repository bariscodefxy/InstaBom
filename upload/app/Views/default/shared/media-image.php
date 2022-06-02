<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $logonPerson  = $this->get("logonPerson");
    $uyelik       = $logonPerson->member;
    $accountInfo  = $this->get("accountInfo");
    $item = $model;
if(isset($item["id"])) {
    ?>
<div class="entry-layout col-lg-4 col-md-4 col-sm-6 col-xs-12" id="entry<?php echo $item["id"]; ?>">
    <div class="entry-thumb transition">
        <div class="entry-media">
            <?php if($accountInfo["user"]["pk"] != $item["user"]["pk"]) { ?>
                <div class="text-absolute">
                    <a href="/user/<?php echo $item["user"]["pk"]; ?>"><img class="img-circle lazy" style="max-width:24px;" data-original="<?php echo str_replace("http:", "https:", $item["user"]["profile_pic_url"]); ?>"/>
                        <strong><?php echo $item["user"]["username"]; ?></strong></a>
                </div>
            <?php } ?>
            <div class="image">
                <a class="lightGalleryImage" id="lightGallery<?php echo $item["id"]; ?>" data-sub-html='<div class="fb-comments" data-id="<?php echo $item["id"]; ?>" id="comments<?php echo $item["id"]; ?>"><p class="text-center"><i class="fa fa-spinner fa-spin fa-4x active"></i></p></div>' href="<?php echo str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>">
                    <img class="img-responsive lazy" data-original="<?php echo str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>"/>
                </a>
            </div>
        </div>
        <div class="action-links">
            <div class="btn-group btn-group-justified">
                <a class="islemBtn btn btn-default" data-liked="<?php echo $item["has_liked"] == 1 ? '1' : '0'; ?>" onclick="like('<?php echo $item["id"]; ?>');" id="item<?php echo $item["id"]; ?>"><i class="fa fa-heart<?php echo $item["has_liked"] == 1 ? ' text-danger' : ''; ?>"></i>
                    <span class="like_count"><?php echo $item["like_count"]; ?></span></a>
                <a class="btn btn-default" onclick="$('#lightGallery<?php echo $item["id"]; ?>').trigger('click')"><i class="fa fa-comment"></i>
                    <span class="comment_count"><?php echo isset($item["comment_count"]) ? $item["comment_count"] : ""; ?></span></a>
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-ellipsis-h"></i></button>
                    <ul class="dropdown-menu  dropdown-menu-right" role="menu">
                        <li>
                            <a download href="<?php echo str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>">Resmi Kaydet</a>
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