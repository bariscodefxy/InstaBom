<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $media
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $media       = NULL;
    if($this->has("media")) {
        $media = $this->get("media");
    }
?>

    <h4>Oto Yorum</h4>
<?php if(is_null($media)) { ?>
    <p>Oto yorum göndereceğiniz gönderi sahibi profil gizli olmamalıdır! Gizli profillerin gönderilerine ulaşılamadığından, yorum da gönderilememektedir.</p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Oto Yorum Ekle
        </div>
        <div class="panel-body">
            <form method="post" action="?formType=findMediaID" class="form">
                <div class="form-group">
                    <label>Gönderi Url'si:</label>
                    <input type="text" name="mediaUrl" class="form-control" placeholder="https://www.instagram.com/p/3H0-Yqjo7u/" required>
                </div>
                <button type="submit" class="btn btn-success">Gönderiyi Bul</button>
            </form>
        </div>
    </div>
<?php } elseif($media["items"][0]["user"]["is_private"] == 1) { ?>
    <hr/>
    <p class="text-danger">Uppps! Bu gönderiyi paylaşan profil gizli. Gizli profillerin gönderilerine ulaşılamadığından, yorum da gönderilememektedir.</p>
<?php } elseif(isset($media["items"][0]["comments_disabled"]) && $media["items"][0]["comments_disabled"] == 1) { ?>
    <hr/>
    <p class="text-danger">Uppps! Bu gönderi yorumlara kapalı.</p>
    <?php
} else { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Oto Yorum Ekle
        </div>
        <div class="panel-body">
            <form id="formYorum" class="form">
                <div class="form-group">
                    <label>Gönderi:</label>
                    <?php $item = $media["items"][0]; ?>
                    <img src="<?php echo $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>" class="img-responsive" style="max-height: 200px;"/>
                </div>
                    <div class="form-group">
                        <label>Cinsiyet:</label>
                        <select name="gender" class="form-control">
                            <option value="0">Karışık</option>
                            <option value="1">Erkek</option>
                            <option value="2">Bayan</option>
                        </select>
                    </div>
                <div class="form-group">
                    <label>Yorumlar:</label>
                    <?php
                        $sampleComments = array(
                            "Woww. Süper görünüyor :)",
                            "Gerçekten harikaaaa..",
                            "Çoook güzeeel.",
                            "Vayy be.",
                            "Bayıldım buna.",
                            "Valla ne desem bilemedim, süper."
                        );
                    ?>
                    <textarea class="form-control" name="yorum" style="height: 250px;"><?php
                            foreach($sampleComments as $comment) {
                                echo $comment . "\n";
                            }
                        ?></textarea>
                    <span class="help-block">Her satıra 1 yorum gelecek şekilde yorumları yazınız. Yazdığınız yorum adedi kadar gönderim yapılacaktır. Mükerrer yorum paylaşımı yapılmaz.</span>
                </div>
                <input type="hidden" name="mediaID" value="<?php echo $item["id"]; ?>">
                <input type="hidden" name="mediaCode" value="<?php echo $item["code"]; ?>">
                <input type="hidden" name="userID" value="<?php echo $item["user"]["pk"]; ?>">
                <input type="hidden" name="userName" value="<?php echo $item["user"]["username"]; ?>">
                <input type="hidden" name="imageUrl" value="<?php echo $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>">
                <input type="hidden" name="_method" value="POST">
                <button type="submit" id="formYorumSubmitButton" class="btn btn-success">Ekle</button>
            </form>
            <div class="cl10"></div>
            <div id="userList"></div>
        </div>
    </div>
<?php } ?>