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
        <form method="post" action="?formType=updatePackage&paketID=<?php echo $paket["id"]; ?>">
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
                    <label>Gönderi Başına Beğeni Adedi</label>
                    <input class="form-control" type="number" name="likeCount" value="<?php echo $paket["likeCount"]; ?>" required>
                    <span class="help-block">Bu alanda yapacağınız değişiklik sadece şu andan sonraki gönderileri kapsar.</span>
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
                    <label>Cinsiyet:</label>
                    <select name="gender" class="form-control">
                        <option value="0">Karışık</option>
                        <option value="1"<?php echo $paket["gender"] == 1 ? ' selected="selected"' : ''; ?>>Erkek</option>
                        <option value="2"<?php echo $paket["gender"] == 2 ? ' selected="selected"' : ''; ?>>Bayan</option>
                    </select>
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
                                    <label class="label label-success"><?php echo intval($paket["likeCount"]) - intval($item["likeCountLeft"]); ?></label>
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