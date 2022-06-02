<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $model
     */
    $isAjaxLoaded = intval($this->get("ajaxLoaded")) == 1 ? TRUE : FALSE;
    if(!$isAjaxLoaded) { ?>
        <div id="entry-list-container">
        <div class="row" id="entry-list-row">
    <?php }
    $allitems = isset($model["feed_items"]) ? $model["feed_items"] : $model["items"];
    foreach($allitems as $item) {
        if(isset($item["media_or_ad"])) {
            $item = $item["media_or_ad"];
        }
        switch($item["media_type"]) {
            //Resim
            case 1:
                $mediaTip = "image";
                break;
            //Video
            case 2:
                $mediaTip = "video";
                break;
            //Çoklu Gönderi
            case 8:
                $mediaTip = "carousel";
                break;
            // 3. Reklam, reklamları yayınlamıyoruz, Diğer rakamları henüz bilmiyoruz hiç denk gelmedi.
            case 3:
            default:
                $mediaTip = NULL;
        }
        if($mediaTip != NULL) {
            $this->renderView("admin/insta/media-" . $mediaTip, $item);
        }
    }
?>
<?php if($model["more_available"] == 1) { ?>
    <button id="btnLoadMore" onclick="loadMore('<?php echo $model["next_max_id"]; ?>');" class="btn btn-block btn-success" type="button">
        <i class="fa fa-plus"></i> Daha Fazla Yükle
    </button>
<?php }
    if(!$isAjaxLoaded) {
        ?>
        </div>
        </div>
    <?php } ?>