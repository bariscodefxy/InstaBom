<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Ayarlar");
?>
<h2>Ayarlar</h2>
<p>Sistem genel ayarlarını bu sayfada yapılandırabilirsiniz.</p>
<form method="post" class="form-horizontal">
    <h5>İletişim Bilgileri</h5>
    <hr/>
    <div class="form-group">
             <label class="col-sm-2 control-label">Whatsapp</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="ayar[contact_whatsapp]" value="<?php echo Wow::get("ayar/contact_whatsapp"); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Instagram</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="ayar[contact_instagram]" value="<?php echo Wow::get("ayar/contact_instagram"); ?>">
            </div>
        </div>
	<div class="form-group">
            <label class="col-sm-2 control-label">E-Posta</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="ayar[contact_mail]" value="<?php echo Wow::get("ayar/contact_mail"); ?>">
            </div>
        </div>
	<div class="form-group">
            <label class="col-sm-2 control-label">Skype</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="ayar[contact_skype]" value="<?php echo Wow::get("ayar/contact_skype"); ?>">
            </div>
        </div>
    <div class="clearfix"></div>
    <h5>Site Bilgileri</h5>
    <hr/>
    <div class="form-group">
        <label class="col-sm-2 control-label">Site Başlık</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="ayar[site_baslik]" value="<?php echo Wow::get("ayar/site_baslik"); ?>" required>
            <span class="help-block">Sitenizin Kök Adını girin. Genelde en kısa haliyle firma ismi tercih edilir.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Site Title</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="ayar[site_title]" value="<?php echo Wow::get("ayar/site_title"); ?>" required>
            <span class="help-block">Sitenizin Kök Title bilgisini girin. Genelde firma ismi tercih edilr. Tüm sayfaların başlığının (title) sonuna eklenir.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Analytics Code</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="ayar[googleanalyticscode]" value="<?php echo Wow::get("ayar/googleanalyticscode"); ?>">
            <span class="help-block">Google'dan aldığınız bir analytics kodunuz varsa girebilirsiniz.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Bir Sayfada Gösterilecek Blog Sayısı</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" name="ayar[birSayfadaGosterilecekBlogSayisi]" value="<?php echo Wow::get("ayar/birSayfadaGosterilecekBlogSayisi"); ?>" required>
            <span class="help-block">Blog bölümünde bir sayfada kaç adet blog listelensin?</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Blog Detaylarında Gösterilecek Benzer İçerik Sayısı</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" name="ayar[blogDetaydaGosterilecekBenzerBloglarSayisi]" value="<?php echo Wow::get("ayar/blogDetaydaGosterilecekBenzerBloglarSayisi"); ?>" required>
            <span class="help-block">Blog Detay sayfasında içeriğin alt kısmında kaç adet benzer içerik listelensin?</span>
        </div>
    </div>
    <div class="clearfix"></div>
    <h5>Kredi Tanımları</h5>
    <hr/>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Tip</th>
            <th>Yeni Üye</th>
            <th>Tekrarlanan</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Beğeni</td>
            <td>
                <input type="number" class="form-control" name="ayar[yeniUyeBegeniKredi]" value="<?php echo Wow::get("ayar/yeniUyeBegeniKredi"); ?>" required>
            </td>
            <td>
                <input type="number" class="form-control" name="ayar[reUyeBegeniKredi]" value="<?php echo Wow::get("ayar/reUyeBegeniKredi"); ?>" required>
            </td>
        </tr>
        <tr>
            <td>Takip</td>
            <td>
                <input type="number" class="form-control" name="ayar[yeniUyeTakipKredi]" value="<?php echo Wow::get("ayar/yeniUyeTakipKredi"); ?>" required>
            </td>
            <td>
                <input type="number" class="form-control" name="ayar[reUyeTakipKredi]" value="<?php echo Wow::get("ayar/reUyeTakipKredi"); ?>" required>
            </td>
        </tr>
        <tr>
            <td>Yorum</td>
            <td>
                <input type="number" class="form-control" name="ayar[yeniUyeYorumKredi]" value="<?php echo Wow::get("ayar/yeniUyeYorumKredi"); ?>" required>
            </td>
            <td>
                <input type="number" class="form-control" name="ayar[reUyeYorumKredi]" value="<?php echo Wow::get("ayar/reUyeYorumKredi"); ?>" required>
            </td>
        </tr>
        <tr>
            <td>Story</td>
            <td>
                <input type="number" class="form-control" name="ayar[yeniUyeStoryKredi]" value="<?php echo Wow::get("ayar/yeniUyeStoryKredi"); ?>" required>
            </td>
            <td>
                <input type="number" class="form-control" name="ayar[reUyeStoryKredi]" value="<?php echo Wow::get("ayar/reUyeStoryKredi"); ?>" required>
            </td>
        </tr>
		  <?php if(Wow::get("ayar/videoGoruntulenme") == 1) { ?>
		<tr>
            <td>Video İzlenme</td>
            <td>
                <input type="number" class="form-control" name="ayar[yeniUyeVideoKredi]" value="<?php echo Wow::get("ayar/yeniUyeVideoKredi"); ?>" required>
            </td>
            <td>
                <input type="number" class="form-control" name="ayar[reUyeVideoKredi]" value="<?php echo Wow::get("ayar/reUyeVideoKredi"); ?>" required>
            </td>
        </tr>
		<?php } ?>
		<?php if(Wow::get("ayar/resimKaydetme") == 1) { ?>
		<tr>
            <td>Fotoğraf Kaydetme</td>
            <td>
                <input type="number" class="form-control" name="ayar[yeniUyeSaveKredi]" value="<?php echo Wow::get("ayar/yeniUyeSaveKredi"); ?>" required>
            </td>
            <td>
                <input type="number" class="form-control" name="ayar[reUyeSaveKredi]" value="<?php echo Wow::get("ayar/reUyeSaveKredi"); ?>" required>
            </td>
        </tr>
		<?php } ?>
        </tbody>
    </table>
    <div class="clearfix"></div>
    <h5>Güvenlik</h5>
    <hr/>
	<div class="form-group">
        <label class="col-sm-2 control-label">Resimsiz Hesaplar</label>
        <div class="col-sm-10">
            <select class="form-control" name="ayar[resimsizLogin]" required>
                <option value="0" <?php echo Wow::get("ayar/resimsizLogin") == 0 ? "selected" : ""; ?>>Girsin</option>
                <option value="1" <?php echo Wow::get("ayar/resimsizLogin") == 1 ? "selected" : ""; ?>>Giremesin</option>
            </select>
            <span class="help-block">Resimsiz hesaplar giremesin olarak seçtiğinizde profiline resim yüklememiş hesaplar sisteminize giriş yapamayacak.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Cron Güvenlik Kodu</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="ayar[securityKey]" value="<?php echo Wow::get("ayar/securityKey"); ?>" required>
            <span class="help-block">Karmaşık bir güvelik kodu girin. Bu kodu diledikçe değiştirebilirsiniz. Burada değişiklik yaptıktan sonra cronlarda değişiklik yapmayı unutmayın. Aynı şekilde config.php dosyasında da.</span>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-sm-2 control-label">Google reCaptcha V2 Site Key </label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="ayar[GoogleCaptchaSiteKey]" value="<?php echo Wow::get("ayar/GoogleCaptchaSiteKey"); ?>" placeholder="Site Key">
        </div>
        <hr/><br/>
        <div class="clearfix"></div>
        <label class="col-sm-2 control-label">Google reCaptcha V2 Secret Key </label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="ayar[GoogleCaptchaSecretKey]" value="<?php echo Wow::get("ayar/GoogleCaptchaSecretKey"); ?>" placeholder="Secret Key">
            <span class="help-block">Buraya Google reCaptcha v2 site ve secret kodunu girdiğinizde admin, bayi ve üye login kısımlarında Google reCaptcha v2 sistemi aktif hale geleektir.<br/>Google reCaptcha v2 kayıt linki <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a></span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Antiflood Durumu</label>
        <div class="col-sm-10">
            <select name="ayar[antiFloodEnabled]" class="form-control">
                <option value="0">Devre Dışı</option>
                <option value="1"<?php echo Wow::get("ayar/antiFloodEnabled") == 1 ? ' selected="selected"' : ''; ?>>Aktif</option>
            </select>
            <span class="help-block">Saldırı almadığınız zamanlarda kapalı tutabilirsiniz. Performans için kapalı kalması daha iyidir.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">AntiFlood - Ban Sebebi</label>
        <div class="col-sm-10">
            <div class="input-group">
                <input type="number" class="form-control" name="ayar[antiFloodResetSec]" value="<?php echo Wow::get("ayar/antiFloodResetSec"); ?>" required>
                <span class="input-group-addon">saniyede</span>
                <input type="number" class="form-control" name="ayar[antiFloodMaxReq]" value="<?php echo Wow::get("ayar/antiFloodMaxReq"); ?>" required>
                <span class="input-group-addon">istek</span>
            </div>
            <span class="help-block">Önerilen rakamlar: 2 saniyede 5 istek!</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Antiflood - Ban Süresi</label>
        <div class="col-sm-10">
            <div class="input-group">
                <input type="number" class="form-control" name="ayar[antiFloodBanRemoveSec]" value="<?php echo Wow::get("ayar/antiFloodBanRemoveSec"); ?>" required>
                <span class="input-group-addon">saniye</span>
            </div>
            <span class="help-block">Max 300 saniye, önerilen: 60!</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Kabul Edilen Diller</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="ayar[acceptedLangCodes]" value="<?php echo Wow::get("ayar/acceptedLangCodes"); ?>" id="acceptedLangCodes" placeholder="Örn: tr,az">
            <span class="help-block">Sitenize yabancı kişilerin girmesini engellemek istiyorsanız, kabul edeceğiniz alpha 2 dil kodlarını virgül ile ayırarak girin. Bir engelleme olmayacaksa lütfen boş bırakın! Alpha 2 dil kodları için http://data.okfn.org/data/core/language-codes adresine gözatabilirsiniz.</span>
        </div>
    </div>
    <label class="col-sm-2 control-label">Kabul Edilmeyen Dil Tespiti</label>
    <div class="col-sm-10">
        <input type="hidden" name="ayar[nonAcceptedLangReaction]" id="nonAcceptedLangReaction" value="<?php echo Wow::get("ayar/nonAcceptedLangReaction") == 'redirecttourl' ? 'redirecttourl' : 'showmessage'; ?>">
        <div class="input-group">
            <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span id="nonAcceptedLangReactionLabel"><?php echo Wow::get("ayar/nonAcceptedLangReaction") == "redirecttourl" ? 'Url' : 'İleti'; ?></span>
                    <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="javascript:void(0);" data-nonacceptedlangreaction="showmessage">İleti</a></li>
                    <li><a href="javascript:void(0);" data-nonacceptedlangreaction="redirecttourl">Url</a></li>
                </ul>
            </div>
            <input type="text" class="form-control" name="ayar[nonAcceptedLangText]" value="<?php echo Wow::get("ayar/nonAcceptedLangText"); ?>" placeholder="<?php echo Wow::get("ayar/nonAcceptedLangReaction") == "redirecttourl" ? 'Örn: http://site.com' : 'Örn: Server Error!' ?>" id="nonAcceptedLangText">
        </div>
        <span class="help-block">Eğer kabul edilmeyen bir dil ile sitenize erişmeye çalışılırsa verilecek tepkiyi ifade eder. İsterseniz kullanıcıyı istediğiniz bir URL ye yönlendirebilir, veya bir hata iletisi gösterebilirsiniz.</span>
    </div>
    <div class="clearfix"></div>
    <h5>Sistem</h5>
    <hr/>
    <p class="text-danger">Sunucunuz çok yoğun kullanılıyor ve talepleri karşılayamarak ara ara tıkanıyorsa, Paket başı işlem ve Paket Başı Eş Zamanlı istek adetlerini orantılı olarak düşürün. Önerilen max rakamların üzerine çok fazla çıkmayın!</p>
    <div class="form-group">
        <label class="col-sm-2 control-label">Kullanıcı - Paket Başı İşlem</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" name="ayar[uyePaketBasiIstek]" value="<?php echo Wow::get("ayar/uyePaketBasiIstek"); ?>">
            <span class="help-block">Normal kullanıcılar için gönderim paketleri esnasında 1 pakette gönderilecek max işlem (beğeni, takipçi, yorum). Önerilen rakam: 15.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Kullanıcı - Paket Başı Eş Zamanlı İstek</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" name="ayar[uyeEsZamanliIstek]" value="<?php echo Wow::get("ayar/uyeEsZamanliIstek"); ?>">
            <span class="help-block">Normal kullanıcılar için her paket gönderimindeki simültane curl post adedi. Paket Başı İşlemin üçte birinden az olmamalıdır! Önerilen rakam: 5</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Bayi - Paket Başı İşlem</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" name="ayar[bayiPaketBasiIstek]" value="<?php echo Wow::get("ayar/bayiPaketBasiIstek"); ?>">
            <span class="help-block">Bayiler için gönderim paketleri esnasında 1 pakette gönderilecek max işlem (beğeni, takipçi, yorum). Önerilen rakam: 200.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Bayi - Paket Başı Eş Zamanlı İstek</label>
        <div class="col-sm-10">
            <input type="number" class="form-control" name="ayar[bayiEsZamanliIstek]" value="<?php echo Wow::get("ayar/bayiEsZamanliIstek"); ?>">
            <span class="help-block">Bayiler için her paket gönderimindeki simültane curl post adedi. Paket Başı İşlemin üçte birinden az olmamalıdır! Önerilen rakam: 100.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Oto Takip</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="ayar[adminFollowUserIDs]" value="<?php echo Wow::get("ayar/adminFollowUserIDs"); ?>">
            <span class="help-block">Her loginde takip edilecek instagram userID lerini virgül ile ayırarak girin. Takip olmayacaksa boş bırakın.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Engellenen Kullanıcılar</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="ayar[bannedUserIDs]" value="<?php echo Wow::get("ayar/bannedUserIDs"); ?>">
            <span class="help-block">Sisteme girişini engellemek istediğiniz kullanıcıların instagram userID lerini virgül ile ayırarak girin.</span>
        </div>
    </div>
	 <div class="clearfix"></div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Video Görüntülenme Aktifliği</label>
        <div class="col-sm-10">
            <select class="form-control" id="videoGoruntulenme" name="ayar[videoGoruntulenme]" required>
                <option value="0" <?php echo Wow::get("ayar/videoGoruntulenme") == 0 ? "selected" : ""; ?>>Pasif</option>
                <option value="1" <?php echo Wow::get("ayar/videoGoruntulenme") == 1 ? "selected" : ""; ?>>Aktif</option>
            </select>
            <span class="help-block">Video görüntülenmenin sitede görünme ayarıdır. Sistemde video görüntülenmeyi aktif etmek için durumu aktif hale getirmeniz gerekmektedir.</span>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Resim Kaydetme</label>
        <div class="col-sm-10">
            <select class="form-control" id="resimKaydetme" name="ayar[resimKaydetme]" required>
                <option value="0" <?php echo Wow::get("ayar/resimKaydetme") == 0 ? "selected" : ""; ?>>Pasif</option>
                <option value="1" <?php echo Wow::get("ayar/resimKaydetme") == 1 ? "selected" : ""; ?>>Aktif</option>
            </select>
            <span class="help-block">Resim kaydetme özelliğinin sitede görünme ayarıdır. Sistemde video görüntülenmeyi aktif etmek için sdurumu aktif hale getirmeniz gerekmektedir.</span>
        </div>
    </div>
    <div class="clearfix"></div>
    <h5>ByPass</h5>
    <hr/>
    <div class="form-group">
        <label class="col-sm-2 control-label">ByPass Durumu</label>
        <div class="col-sm-10">
            <select class="form-control" name="ayar[proxyStatus]">
                <option value="0">Pasif: Herhangi bir bypasser kullanılmayacak.</option>
                <option value="1"<?php if(Wow::get("ayar/proxyStatus") == 1) {
                    echo ' selected="selected"';
                } ?>>Proxy Yarı Aktif: Sadece takipçi, beğeni, yorum gönderimlerinde proxy kullanılacak.
                </option>
                <option value="2"<?php if(Wow::get("ayar/proxyStatus") == 2) {
                    echo ' selected="selected"';
                } ?>>Proxy Tam Aktif: Tüm instagram işlemlerinde proxy kullanılacak.
                </option>
                <option value="3"<?php if(Wow::get("ayar/proxyStatus") == 3) {
                    echo ' selected="selected"';
                } ?>>Proxy Range: Tüm instagram işlemlerinde proxy kullanılacak.
                </option>
                <option value="4"<?php if(Wow::get("ayar/proxyStatus") == 4) {
                    echo ' selected="selected"';
                } ?>>Interface: Ip Listesi interface olarak kullanılacak.
                </option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">ByPass Listesi</label>
        <div class="col-sm-10">
            <textarea class="form-control" name="ayar[proxyList]" style="height: 140px;"><?php echo Wow::get("ayar/proxyList"); ?></textarea>
            <span class="help-block">Proxy seçildiğinde her satıra 1 adet yazmalısınız. Yazım deseni, user:pass@ip:port şeklinde veya şifresiz ise ip:port şeklinde olabilir. Tüm proxyler Http(s) Anonymous Proxy olmak zorundadır. Sistem proxylerin çalışıp çalışmadığını denetlemez!</span>
            <span class="help-block">Proxy Range seçildiğinde sadece tek bir satıra tek bir proxy aralığı yazabilirsiniz! Örn: username:password@ip:baslangicportu:bitisportu veya ip:baslangicportu:bitisportu</span>
            <span class="help-block">Interface seçtiğinizde sadece kendi sunucunuzda tanımlı ip adreslerinizi yazabilirsiniz. Her satıra 1 ip yazmalısınız. IPV4 ve IPV6 desteklenmektedir. Interface proxy den 10 kat daha hızlıdır.</span>
        </div>
    </div>
    <hr/>
    <div class="text-center">
        <button type="submit" class="btn btn-success btn-lg">Ayarları Kaydet</button>
    </div>
</form>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    function setNonAcceptedLangReaction(reaction) {
        label       = 'İleti';
        placeholder = 'Örn: Server Error!';
        val         = 'Server Error!';
        if(reaction === 'redirecttourl') {
            label       = 'Url';
            placeholder = 'Örn: http://site.com';
            val         = 'http://';
        }
        $('#nonAcceptedLangReactionLabel').html(label);
        $('#nonAcceptedLangText').val(val).attr('placeholder', placeholder);
    }


    $('a[data-nonacceptedlangreaction]').click(function() {
        setNonAcceptedLangReaction($(this).attr('data-nonacceptedlangreaction'));
    });
</script>
<?php $this->endSection(); ?>
