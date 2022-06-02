<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $paket      = $model["paket"];
    $paketdetay = $model["paketdetay"];
?>
<ul class="nav nav-tabs nav-justified nav-tabs-justified">
    <li class="active"><a href="#tabLikePackageDetails" data-toggle="tab">Paket Detayları</a></li>
    <li><a href="#tabLikePackageHistory" data-toggle="tab">Gönderim Geçmişi</a></li>
</ul>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade active in" id="tabLikePackageDetails">
        <form method="post" action="<?php echo Wow::get("project/adminPrefix") . "/instamis/edit-auto-like-package/" . $paket["id"]; ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label>Başlama Tarihi</label>
                    <input class="form-control" type="text" name="startDate" value="<?php echo date("d.m.Y H:i:s", strtotime($paket["startDate"])); ?>" required>
                    <span class="help-block">gg.aa.yyyy ss:dd:ss formatında girin</span>
                </div>
                <div class="form-group">
                    <label>Sona Erme Tarihi</label>
                    <input class="form-control" type="text" name="endDate" value="<?php echo date("d.m.Y H:i:s", strtotime($paket["endDate"])); ?>" required>
                    <span class="help-block">gg.aa.yyyy ss:dd:ss formatında girin</span>
                </div>
                <div class="form-group">
                    <label>Gönderi Başına Beğeni Sayısı:</label>
                    <div class="input-group">
                        <span class="input-group-addon">Min</span>
                        <input type="number" name="likeCountMin" class="form-control" value="<?php echo $paket["likeCountMin"]; ?>" required>
                        <span class="input-group-addon">Max</span>
                        <input type="number" name="likeCountMax" class="form-control" value="<?php echo $paket["likeCountMax"]; ?>" required>
                    </div>
                    <span class="help-block">Gönderi başına min 1 girebilirsiniz. Tam bir sayı belirtmek istiyorsanız her 2 alana da aynı rakamı girmelisiniz! Sistem paylaşılan her gönderi için, girdiğiniz min ve max rakamlar arasında rasgele bir sayı belirler ve bu sayı kadar beğeni gönderir. Her gönderiye aynı sayıda beğeni gönderilmesi müşterilerde rahatsızlık oluşturduğu için bu opsiyon eklenmiştir!</span>
                </div>
                <div class="form-group">
                    <label>Beğeni Gönderim Sıklığı</label>
                    <input class="form-control" type="number" name="minuteDelay" value="<?php echo $paket["minuteDelay"]; ?>" required>
                    <span class="help-block">Tespit edilen gönderilere, kaç dakikada bir beğeni gönderilecek?</span>
                </div>
                <div class="form-group">
                    <label>Sıklık Başına Beğeni Gönderim Adedi</label>
                    <input class="form-control" type="number" name="krediDelay" value="<?php echo $paket["krediDelay"]; ?>" required>
                    <span class="help-block">Yukarıda yazdığınız x dakikada bir kaç beğeni gönderilecek.</span>
                    <span class="help-block text-danger">Bu alan Gönderi Başına Beğeni Adedi'nin 20de birinden küçük ve 250den büyük olamaz! Küçük girerseniz 20de biri olarak, büyük girerseniz 250 olarak ayarlanır!</span>
                </div>
                <div class="form-group">
                    <label>Durum</label>
                    <select name="isActive" class="form-control">
                        <option value="0"<?php echo $paket["isActive"] == 0 ? ' selected="selected"' : ''; ?>>Pasif</option>
                        <option value="1"<?php echo $paket["isActive"] == 1 ? ' selected="selected"' : ''; ?>>Aktif</option>
                        <option value="2"<?php echo $paket["isActive"] == 2 ? ' selected="selected"' : ''; ?>>Süre Bitti</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
    <div class="tab-pane fade" id="tabLikePackageHistory">
        <div class="modal-body">
            <?php if(empty($paketdetay)) { ?>
                <p class="text-primary">Henüz veri yok!</p>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Medya</th>
                            <th>Gönderilen</th>
                            <th>Kalan</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($paketdetay as $item) { ?>
                            <tr>
                                <td>
                                    <img style="max-height: 100px;" src="<?php echo str_replace("http:", "https:", $item["imageUrl"]); ?>"/>
                                </td>
                                <td>
                                    <label class="label label-success"><?php echo intval($item["likeCountTotal"]) - intval($item["likeCountLeft"]); ?></label>
                                </td>
                                <td>
                                    <label class="label label-warning"><?php echo intval($item["likeCountLeft"]); ?></label>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</div>