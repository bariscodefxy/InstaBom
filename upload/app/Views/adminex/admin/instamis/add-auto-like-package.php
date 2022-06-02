<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
?>
<form method="post" action="<?php echo Wow::get("project/adminPrefix") . "/instamis/add-auto-like-package/" . $model["user"]["pk"]; ?>">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Paket Tanımla</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <img src="<?php echo str_replace("http:", "https:", $model["user"]["profile_pic_url"]); ?>" style="max-height: 50px" />
        </div>
        <div class="form-group">
            <label>Başlama Tarihi</label>
            <input class="form-control" type="text" name="startDate" value="<?php echo date("d.m.Y H:i:s"); ?>" required>
            <span class="help-block">gg.aa.yyyy ss:dd:ss formatında girin</span>
        </div>
        <div class="form-group">
            <label>Sona Erme Tarihi</label>
            <input class="form-control" type="text" name="endDate" value="<?php echo date("d.m.Y H:i:s", strtotime("+7 days")); ?>" required>
            <span class="help-block">gg.aa.yyyy ss:dd:ss formatında girin</span>
        </div>
        <div class="form-group">
            <label>Gönderi Başına Beğeni Sayısı:</label>
            <div class="input-group">
                <span class="input-group-addon">Min</span>
                <input type="number" name="likeCountMin" class="form-control" placeholder="100" value="100" required>
                <span class="input-group-addon">Max</span>
                <input type="number" name="likeCountMax" class="form-control" placeholder="200" value="200" required>
            </div>
            <span class="help-block">Gönderi başına min 1 girebilirsiniz. Tam bir sayı belirtmek istiyorsanız her 2 alana da aynı rakamı girmelisiniz! Sistem paylaşılan her gönderi için, girdiğiniz min ve max rakamlar arasında rasgele bir sayı belirler ve bu sayı kadar beğeni gönderir. Her gönderiye aynı sayıda beğeni gönderilmesi müşterilerde rahatsızlık oluşturduğu için bu opsiyon eklenmiştir!</span>
        </div>
        <div class="form-group">
            <label>Beğeni Gönderim Sıklığı</label>
            <input class="form-control" type="number" name="minuteDelay" value="1" required>
            <span class="help-block">Tespit edilen gönderilere, kaç dakikada bir beğeni gönderilecek?</span>
        </div>
        <div class="form-group">
            <label>Sıklık Başına Beğeni Gönderim Adedi</label>
            <input class="form-control" type="number" name="krediDelay" value="100" required>
            <span class="help-block">Yukarıda yazdığınız x dakikada bir kaç beğeni gönderilecek.</span>
            <span class="help-block text-danger">Bu alan Gönderi Başına Beğeni Adedi'nin 20de birinden küçük ve 250den büyük olamaz! Küçük girerseniz 20de biri olarak, büyük girerseniz 250 olarak ayarlanır!</span>
        </div>
        <div class="form-group">
            <label>Cinsiyet:</label>
            <select name="gender" class="form-control">
                <option value="0">Karışık</option>
                <option value="1">Erkek</option>
                <option value="2">Bayan</option>
            </select>
        </div>
        <input type="hidden" name="userName" value="<?php echo $model["user"]["username"]; ?>">
        <input type="hidden" name="imageUrl" value="<?php echo str_replace("http:", "https:", $model["user"]["profile_pic_url"]); ?>">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
        <button type="submit" class="btn btn-primary">Kaydet</button>
    </div>
</form>