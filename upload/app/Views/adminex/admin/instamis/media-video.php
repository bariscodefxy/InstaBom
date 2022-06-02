<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $logonPerson = $this->get("logonPerson");
    $uyelik      = $logonPerson->member;
    $accountInfo = $this->get("accountInfo");
    $item        = $model;
?>
<div class="entry-layout col-lg-4 col-md-4 col-sm-6 col-xs-12" id="entry<?php echo $item["id"]; ?>">
    <div class="entry-thumb transition">
        <div class="entry-media">
            <div class="image">
                <a data-fancybox href="#video<?php echo $item["id"]; ?>">
                    <img class="img-responsive" src="<?php echo str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>"/>
                    <div class="play-icon">
                        <?php echo intval($item["video_duration"]) . "sn"; ?>
                    </div>
                    <div style="display: none;">
                        <div id="video<?php echo $item["id"]; ?>">
                            <video width="100%" controls>
                                <source src="<?php echo str_replace("http:", "https:", $item["video_versions"][0]["url"]); ?>" type="video/mp4">
                            </video>
                        </div>
                    </div>
                </a>
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
                            <a download href="<?php echo str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>">Resmi Kaydet</a>
                        </li>
                        <li>
                            <a download href="<?php echo str_replace("http:", "https:", $item["video_versions"][0]["url"]); ?>">Video'yu Kaydet</a>
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
