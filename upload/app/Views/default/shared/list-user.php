<?php /**
 * @var \Wow\Template\View      $this
 * @var array                   $model
 * @var \App\Models\LogonPerson $logonPerson
 */
    $logonPerson  = $this->get("logonPerson");
    $isAjaxLoaded = intval($this->get("ajaxLoaded")) == 1 ? TRUE : FALSE;
    $accountInfo  = $this->get("accountInfo");
    if(!$isAjaxLoaded) { ?>
        <div id="entry-list-container">
        <div class="row" id="entry-list-row">
    <?php }
    foreach($model["users"] as $item) {
        ?>
        <div class="entry-layout col-lg-3 col-md-3 col-sm-6 col-xs-12" id="entry<?php echo $item["pk"]; ?>">
            <div class="entry-thumb transition">
                <div class="entry-media">
                    <?php if($accountInfo["user"]["pk"] != $item["pk"]) { ?>
                        <div class="text-absolute">
                            <a href="/user/<?php echo $item["pk"]; ?>"><strong><?php echo $item["username"]; ?></strong></a>
                        </div>
                    <?php } ?>
                    <div class="image">
                        <a href="/user/<?php echo $item["pk"]; ?>"><img class="img-responsive lazy" data-original="<?php echo str_replace("http:", "https:", $item["profile_pic_url"]); ?>"/></a>
                    </div>
                </div>
                <div class="action-links">
                    <div class="btn-group btn-group-justified">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-ellipsis-h"></i></button>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a download href="<?php echo str_replace("http:", "https:", str_replace("s150x150/", "", $item["profile_pic_url"])); ?>">Resmi Kaydet</a>
                                </li>
                                <?php if($logonPerson->member->instaID != $item["pk"]) { ?>
                                    <li>
                                        <a href="javascript:void(0);" onclick="newMessage('<?php echo $item["pk"]; ?>')">DM Gönder</a>
                                    </li>
                                <?php } ?>
                                <li>
                                    <a target="_blank" href="https://www.instagram.com/<?php echo $item["username"]; ?>">Instagram'da Görüntüle</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
?>
<?php if(isset($model["next_max_id"])) { ?>
    <button id="btnLoadMore" onclick="loadMore('<?php echo $model["next_max_id"]; ?>');" class="btn btn-block btn-success" type="button">
        <i class="fa fa-plus"></i> Daha Fazla Yükle
    </button>
<?php }
    if(!$isAjaxLoaded) {
        ?>
        </div>
        </div>
    <?php } ?>