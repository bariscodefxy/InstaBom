<?php
	/**
	 * @var \Wow\Template\View      $this
	 * @var array                   $media
	 * @var \App\Models\LogonPerson $logonPerson
	 */
	$logonPerson = $this->get("logonPerson");
	$user        = NULL;
	if($this->has("user")) {
		$user = $this->get("user");
	}
?>
    <h4>Oto Beğeni Tanımlama</h4>
<?php if(is_null($user)) { ?>
    <p>Oto Beğeni Tanımlama aracı ile, dilediğiniz kullanıcıya, kendi belirlediğiniz adette gönderi başına beğeniyi kaydedebilirsiniz. Kullanıcının profili açık olmalıdır.</p>
    <p>Bitiş tarihine kadar, kullanıcının tüm gönderilerine belirlediğiniz adette beğeni yapılacaktır.</p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Oto Beğeni Paketi Tanımla
        </div>
        <div class="panel-body">
            <form method="post" action="?formType=findUserID" class="form">
                <div class="form-group">
                    <label>Kullanıcı Adı:</label>
                    <input type="text" name="username" class="form-control" placeholder="fatihh" required>
                </div>
                <button type="submit" class="btn btn-success">Kullanıcıyı Bul</button>
            </form>
        </div>
    </div>
<?php } else { ?>
    <p><strong>Kalan İşlem Hakkınız:</strong> <?php echo $logonPerson->member->toplamOtoBegeniLimitLeft; ?>
    </p>
    <p>
        <strong>İşlem Başına Tanımlayabileceğiniz Max Gönderi Başına Beğeni:</strong> <?php echo $logonPerson->member->otoBegeniMaxKredi; ?>
    </p>
    <div class="panel panel-default">
        <div class="panel-heading">
            Oto Beğeni Tanımla
        </div>
        <div class="panel-body">
            <form id="formOtoBegeni" action="?formType=send" class="form" method="post" onsubmit="return controlBayiForm();">
                <div class="form-group">
                    <label><?php echo "@".$user["user"]["username"]; ?></label>
                    <img src="<?php echo str_replace("http:","https:",$user["user"]["profile_pic_url"]); ?>" class="img-responsive" style="max-height: 200px;"/>
                </div>
                <div class="form-group">
                    <label>Başlama Tarihi</label>
                    <input class="form-control" type="text" name="startDate" value="<?php echo date("d.m.Y H:i:s"); ?>" required>
                    <span class="help-block">gg.aa.yyyy ss:dd:ss formatında girin. Tarih şu anki zamandan küçük olamaz. Geçmiş bir zaman yazarsanız şimdiki zaman olarak kabul edeceğiz.</span>
                </div>
                <div class="form-group">
                    <label>Oto Beğeni Süresi</label>
                    <select class="form-control" name="days" required>
						<?php for($i = 1;$i <= 365;$i++) { ?>
                            <option value="<?php echo $i; ?>"<?php echo 365 == $i ? ' selected="selected"' : ''; ?>><?php echo $i." gün"; ?></option>
						<?php } ?>
                    </select>
                    <span class="help-block">Max <?php echo 365 ?> gün olabilir.</span>
                </div>
                <div class="form-group">
                    <label>Gönderi Başına Beğeni Sayısı:</label>
                    <div class="input-group">
                        <span class="input-group-addon">Min</span>
                        <input type="number" name="minadet" class="form-control" placeholder="100" value="100" required>
                        <span class="input-group-addon">Max</span>
                        <input type="number" name="maxadet" class="form-control" placeholder="200" value="200" required>
                    </div>
                    <span class="help-block">Gönderi başına min 1, max <?php echo $logonPerson->member->otoBegeniMaxKredi; ?> girebilirsiniz. Tam bir sayı belirtmek istiyorsanız her 2 alana da aynı rakamı girmelisiniz! Sistem paylaşılan her gönderi için, girdiğiniz min ve max rakamlar arasında rasgele bir sayı belirler ve bu sayı kadar beğeni gönderir. Her gönderiye aynı sayıda beğeni gönderilmesi müşterilerde rahatsızlık oluşturduğu için bu opsiyon eklenmiştir!</span>
                </div>
                <div class="form-group">
                    <label>Beğeni Gönderim Sıklığı</label>
                    <input class="form-control" type="number" name="minuteDelay" value="1" required>
                    <span class="help-block">Tespit edilen gönderilere, kaç dakikada bir beğeni gönderilecek?</span>
                </div>
                <div class="form-group">
                    <label>Sıklık Başına Beğeni Gönderim Adedi</label>
                    <input class="form-control" type="number" name="krediDelay" value="250" required>
                    <span class="help-block">Yukarıda yazdığınız x dakikada bir kaç beğeni gönderilecek.</span>
                    <span class="help-block text-danger">Bu alan Gönderi Başına Beğeni Adedi'nin 20de birinden küçük ve 250den büyük olamaz! Küçük girerseniz 20de biri olarak, büyük girerseniz 250 olarak ayarlanır!</span>
                </div>
				<?php if($logonPerson->member->otoBegeniGender === 1) { ?>
                    <div class="form-group">
                        <label>Cinsiyet:</label>
                        <select name="gender" class="form-control">
                            <option value="0">Karışık</option>
                            <option value="1">Erkek</option>
                            <option value="2">Bayan</option>
                        </select>
                    </div>
				<?php } ?>
                <input type="hidden" name="userID" value="<?php echo $user["user"]["pk"]; ?>">
                <input type="hidden" name="userName" value="<?php echo $user["user"]["username"]; ?>">
                <input type="hidden" name="imageUrl" value="<?php echo str_replace("http:","https:",$user["user"]["profile_pic_url"]); ?>">
                <button type="submit" id="formOtoBegeniSubmitButton" class="btn btn-success">Ekle</button>
            </form>
        </div>
    </div>
<?php } ?>

<?php $this->section("section_scripts");
	$this->parent();
	if(!is_null($user)) { ?>
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
	<?php }
	$this->endSection(); ?>