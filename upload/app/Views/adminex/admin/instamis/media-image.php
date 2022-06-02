<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $item = $model;
?>
<div class="entry-layout col-lg-4 col-md-4 col-sm-6 col-xs-12" id="entry<?php echo $item["id"]; ?>">
    <div class="entry-thumb transition">
        <div class="entry-media">
            <div class="image">
                <a data-fancybox href="<?php echo str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>">
                    <img class="img-responsive" src="<?php echo str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>"/>
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
                            <a href="https://www.instagram.com/p/<?php echo $item["code"]; ?>" target="_blank">Instagram'da Göster</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
