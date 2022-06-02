<?php
	/**
	 * @var \Wow\Template\View      $this
	 * @var array                   $model
	 * @var \App\Models\LogonPerson $logonPerson
	 */
	$logonPerson = $this->get("logonPerson");
	$paket       = $model["paket"];
	$paketdetay  = $model["paketdetay"];
?>
<ul class="nav nav-tabs nav-justified nav-tabs-justified">
    <li class="active"><a href="#tabLikePackageDetails" data-toggle="tab">Paket Detayları</a></li>
    <li><a href="#tabLikePackageHistory" data-toggle="tab">Gönderim Geçmişi</a></li>
</ul>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade active in" id="tabLikePackageDetails">
        <div class="modal-body">
            <form id="formOtoBegeni" action="?formType=updateAutoLikePackage&bayiIslemID=<?php echo $paket["bayiIslemID"]; ?>" class="form" method="post" onsubmit="return controlBayiForm();">
                <div class="form-group">
                    <label>Başlama Tarihi</label>
                    <p><?php echo date("d.m.Y H:i:s",strtotime($paket["startDate"])); ?></p>
                </div>
                <div class="form-group">
                    <label>Sona Erme Tarihi</label>
                    <p><?php echo date("d.m.Y H:i:s",strtotime($paket["endDate"])); ?></p>
                </div>
                <div class="form-group">
                    <label>Gönderi Başına Beğeni Sayısı:</label>
                    <div class="input-group">
                        <span class="input-group-addon">Min</span>
                        <input type="number" name="minadet" class="form-control" placeholder="100" value="<?php echo $paket["minKrediTotal"]; ?>" required>
                        <span class="input-group-addon">Max</span>
                        <input type="number" name="maxadet" class="form-control" placeholder="200" value="<?php echo $paket["maxKrediTotal"]; ?>" required>
                    </div>
                    <span class="help-block">Gönderi başına min 1, max <?php echo $logonPerson->member->otoBegeniMaxKredi; ?> girebilirsiniz. Tam bir sayı belirtmek istiyorsanız her 2 alana da aynı rakamı girmelisiniz! Sistem paylaşılan her gönderi için, girdiğiniz min ve max rakamlar arasında rasgele bir sayı belirler ve bu sayı kadar beğeni gönderir. Her gönderiye aynı sayıda beğeni gönderilmesi müşterilerde rahatsızlık oluşturduğu için bu opsiyon eklenmiştir!</span>
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
				<?php if($logonPerson->member->otoBegeniGender === 1) { ?>
                    <div class="form-group">
                        <label>Cinsiyet:</label>
                        <select name="gender" class="form-control">
                            <option value="0">Karışık</option>
                            <option value="1"<?php echo $paket["gender"] == 1 ? ' selected="selected"' : ''; ?>>Erkek</option>
                            <option value="2"<?php echo $paket["gender"] == 2 ? ' selected="selected"' : ''; ?>>Bayan</option>
                        </select>
                    </div>
				<?php } ?>
                <div class="form-group">
                    <label>Durum</label>
					<?php $arrIsActive = array(
						0 => "Pasif",
						1 => "Aktif",
						2 => "Süre Bitti"
					); ?>
                    <select class="form-control" name="isActive">
						<?php foreach($arrIsActive as $k => $v) { ?>
                            <option value="<?php echo $k; ?>"<?php echo $k == $paket["isActive"] ? ' selected="selected"' : ''; ?>><?php echo $v; ?></option>
						<?php } ?>
                    </select>
                </div>
                <button type="submit" id="formOtoBegeniSubmitButton" class="btn btn-success">Değişiklikleri Kaydet</button>
            </form>
            <script type="text/javascript">
                function controlBayiForm() {
                    countBegeniMin = parseInt($('#formOtoBegeni input[name=minadet]').val());
                countBegeniMax = parseInt($('#formOtoBegeni input[name=maxadet]').val());
                if(isNaN(countBegeniMin) || countBegeniMin <= 0) {
                    alert('Min beğeni adeti 1 ila <?php echo $logonPerson->member->otoBegeniMaxKredi; ?> arası olmalı!');
                    return false;
                }
                if(isNaN(countBegeniMax) || countBegeniMax <= 0) {
                    alert('Max beğeni adeti 1 ila <?php echo $logonPerson->member->otoBegeniMaxKredi; ?> arası olmalı!');
                    return false;
                }
                if(countBegeniMax > <?php echo $logonPerson->member->otoBegeniMaxKredi; ?>) {
                    alert('Beğeni adedi max <?php echo $logonPerson->member->otoBegeniMaxKredi; ?> olabilir!');
                    return false;
                }
                if(countBegeniMin > countBegeniMax) {
                    alert('Min beğeni adedi max adetten fazla olamaz!');
                    return false;
                }
                    $('#formOtoBegeniSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Bekleyin..');
                    $('#formOtoBegeni button').attr('disabled', 'disabled');
                }
            </script>
        </div>
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
                                    <img style="max-height: 100px;" src="<?php echo str_replace("http:","https:",$item["imageUrl"]); ?>"/>
                                </td>
                                <td>
                                    <label class="label label-success"><?php echo intval($item["likeCountTotal"])-intval($item["likeCountLeft"]); ?></label>
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