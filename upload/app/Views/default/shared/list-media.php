<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $model
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson  = $this->get("logonPerson");
    $uyelik       = $logonPerson->member;
    $isAjaxLoaded = intval($this->get("ajaxLoaded")) == 1 ? TRUE : FALSE;
    $accountInfo  = $this->get("accountInfo");

    if(!$isAjaxLoaded) { ?>
        <div id="entry-list-container">
        <div class="row" id="entry-list-row">
    <?php }
    if(isset($model["ranked_items"])) { ?>
        <div class="col-xs-12"><h4>Popüler Gönderiler</h4></div>
        <?php foreach($model["ranked_items"] as $item) {
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
                $this->renderView("shared/media-" . $mediaTip, $item);
            }
        } ?>
        <div style="clear:both;"></div>
        <div class="col-xs-12">
            <hr/>
            <h4>En Yeni Gönderiler</h4>
        </div>
    <?php }

//    if(!isset($_SESSION["instamisToken"])) {
        $allitems = isset($model["feed_items"]) ? $model["feed_items"] : $model["items"];

        foreach($allitems as $item) {
            if(isset($item["media_or_ad"])) {
                $item = $item["media_or_ad"];
            }

            if(isset($item["media_type"])) {
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
            }

            if($mediaTip != NULL) {
                $this->renderView("shared/media-" . $mediaTip, $item);
            }
//        }

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