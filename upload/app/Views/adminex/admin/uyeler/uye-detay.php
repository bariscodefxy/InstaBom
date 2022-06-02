<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $uye   = $model["uye"];
    $paket = $model["paket"];
    $this->set("title", "Üye Detay: " . $uye["kullaniciAdi"]);
?>
<ul class="nav nav-tabs">
    <li><a href="<?php echo Wow::get("project/adminPrefix") . "/insta/user/" . $uye["instaID"]; ?>">Instagram</a></li>
    <li class="active"><a href="javascript:void(0);">Üye</a></li>
</ul>
<div class="clearfix" style="height: 20px;"></div>
<form method="post" action="?formType=saveUserDetails">
    <div class="panel panel-default">
        <div class="panel-heading">
            Üye Detayları
        </div>
        <div class="panel-body">
            <h3>@<?php echo $uye["kullaniciAdi"]; ?></h3>
            <div class="form-group">
                <label>Beğeni Kredisi</label>
                <input type="number" name="begeniKredi" value="<?php echo $uye["begeniKredi"]; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Takipçi Kredisi</label>
                <input type="number" name="takipKredi" value="<?php echo $uye["takipKredi"]; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Yorum Kredisi</label>
                <input type="number" name="yorumKredi" value="<?php echo $uye["yorumKredi"]; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Story Kredisi</label>
                <input type="number" name="storyKredi" value="<?php echo $uye["storyKredi"]; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Video Görüntülenme Kredisi</label>
                <input type="number" name="videoKredi" value="<?php echo $uye["videoKredi"]; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Resim Kaydetme Kredisi</label>
                <input type="number" name="saveKredi" value="<?php echo $uye["saveKredi"]; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Yorum Beğeni Kredisi</label>
                <input type="number" name="yorumBegeniKredi" value="<?php echo $uye["yorumBegeniKredi"]; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Canlı Yayın Kredisi</label>
                <input type="number" name="canliYayinKredi" value="<?php echo $uye["canliYayinKredi"]; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Kullanımda (Beğeni, Takip, Yorum yazdırılabilir.)</label>
                <div class="checkbox">
                    <label><input type="checkbox" class="checkbox-inline" value="1" name="isUsable"<?php echo $uye["isUsable"] == 1 ? ' checked="checked"' : ''; ?>> Kullanımda</label>
                </div>
                <span class="help-block">Bu kullanıcının beğeni, takip, yorum işlemlerinde kullanılmasını istemiyorsanız işareti kaldırın.</span>
            </div>
            <div class="form-group">
                <label>Bayi</label>
                <div class="checkbox">
                    <label><input type="checkbox" class="checkbox-inline" value="1" name="isBayi"<?php echo $uye["isBayi"] == 1 ? ' checked="checked"' : ''; ?>> Light Bayi</label>
                </div>
                <span class="help-block">Bu kullanıcı kredilerini başkaları için de harcayabilsin istiyorsanız işaretleyin. Bu kısım işaretli olmadığında kredilerini sadece kendi gönderileri için kullanabilir.</span>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
            <a href="<?php echo Wow::get("project/adminPrefix"); ?>/uyeler/uye-sil/<?php echo $uye["uyeID"]; ?>" class="btn btn-danger" onclick="return confirm('Bu üyeyi gerçekten silmek istiyor musunuz?');">Bu Üyeyi Sil</a>
        </div>
    </div>
</form>