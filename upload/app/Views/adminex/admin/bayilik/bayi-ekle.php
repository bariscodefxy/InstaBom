<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
?>
<h4>Yeni Bayi Ekle</h4>
<div class="row">
    <div class="col-md-6">
        <form method="post" action="?formType=saveBayiEkle">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Bayi Detayları
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>Kullanıcı Adı</label>
                        <input type="text" name="username" value="" class="form-control" placeholder="Kullanıcı Adı" required>
                    </div>
                    <div class="form-group">
                        <label>Şifre</label>
                        <input type="text" name="password" value="" class="form-control" placeholder="Şifre" required>
                    </div>
                    <div class="form-group">
                        <label>API Bakiye</label>
                        <input type="text" name="smmbakiye" value="" class="form-control" placeholder="Bakiye" required autocomplete="off">
                        <span class="help-block">Verdiğiniz api servisinin bakiyesidir. Bu bakiye olmadığında apiden işlem yapılamaz.</span>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Beğeni Max. Kredi</label>
                                <input type="number" name="begeniMaxKredi" value="" class="form-control" required placeholder="Max. Beğeni Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Beğeni Ücreti (Api)</label>
                                <input type="text" name="begeniPrice" value="" class="form-control" required placeholder="1K Beğeni ücreti">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Beğeni Cinsiyeti</label>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="begeniGender" value="1">Seçebilir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reel Beğeni Max. Kredi</label>
                                <input type="number" name="begeniMaxKredi" value="<?php echo $bayi["begeniMaxKredi"]; ?>" class="form-control" required placeholder="Max. Reel Beğeni Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Reel Beğeni Ücreti (Api)</label>
                                <input type="text" name="begeniprice" value="<?php echo $bayi["begeniprice"]; ?>" class="form-control" required placeholder="1K Reel Beğeni ücreti">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reel Beğeni Cinsiyeti</label>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="begeniGender" value="1"<?php echo $bayi["begeniGender"] == 1 ? ' checked="checked"' : ''; ?>>Seçebilir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Takip Max. Kredi</label>
                                <input type="number" name="takipMaxKredi" value="" class="form-control" required placeholder="Max. Takip Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Takipçi Ücreti (Api)</label>
                                <input type="text" name="takipPrice" value="" class="form-control" required placeholder="1K Beğeni ücreti">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Takipçi Cinsiyeti</label>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="takipGender" value="1">Seçebilir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Yorum Max. Kredi</label>
                                <input type="number" name="yorumMaxKredi" value="" class="form-control" required placeholder="Max. Yorum Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Yorum Ücreti (Api)</label>
                                <input type="text" name="yorumPrice" value="" class="form-control" required placeholder="1K Yorum ücreti">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Yorum Cinsiyeti</label>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="yorumGender" value="1">Seçebilir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Yorum Beğeni Max. Kredi</label>
                                <input type="number" name="yorumBegeniMaxKredi" value="" class="form-control" required placeholder="Max. Yorum Beğeni Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Yorum Beğeni Ücreti (Api)</label>
                                <input type="text" name="yorumBegeniPrice" value="" class="form-control" required placeholder="Max. Yorum Beğeni Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Yorum Beğeni Cinsiyeti</label>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="yorumBegeniGender" value="1">Seçebilir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Canlı Yayın Max. Kredi</label>
                                <input type="number" name="canliYayinMaxKredi" value="" class="form-control" required placeholder="Max. Canlı Yayın Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Canlı Yayın Ücreti (Api)</label>
                                <input type="text" name="canliYayinPrice" value="" class="form-control" required placeholder="Max. Canlı Yayın Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Canlı Yayın Cinsiyeti</label>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="canliYayinGender" value="1">Seçebilir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Video Görüntülenme Max. Kredi</label>
                                <input type="number" name="videoMaxKredi" value="" class="form-control" required placeholder="Max. Video Görüntülenme Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Görüntülenme Ücreti (Api)</label>
                                <input type="text" name="videoPrice" value="" class="form-control" required placeholder="Max. Video Görüntülenme Kredisi">
                            </div>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reel Video Görüntülenme Max. Kredi</label>
                                <input type="number" name="videoMaxKredi" value="<?php echo $bayi["videoMaxKredi"]; ?>" class="form-control" required placeholder="Max. Reel Video Görüntülenme Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Reel Görüntülenme Ücreti (Api)</label>
                                <input type="text" name="videoPrice" value="<?php echo $bayi["videoPrice"]; ?>" class="form-control" required placeholder="Max. Reel Video Görüntülenme Kredisi">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kaydetme Max. Kredi</label>
                                <input type="number" name="saveMaxKredi" value="" class="form-control" required placeholder="Max. Kaydetme Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Kaydetme Ücreti (Api)</label>
                                <input type="text" name="savePrice" value="" class="form-control" required placeholder="Max. Kaydetme Kredisi">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Story Max. Kredi</label>
                                <input type="number" name="storyMaxKredi" value="" class="form-control" required placeholder="Max. Story Kredisi">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>1K Story Ücreti (Api)</label>
                                <input type="text" name="storyPrice" value="" class="form-control" required placeholder="Max. Story Kredisi">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Oto Beğeni Max. Kredi</label>
                                <input type="number" name="otoBegeniMaxKredi" value="" class="form-control" required placeholder="Oto Beğeni Max. Kredi">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Oto Beğeni Cinsiyeti</label>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="otoBegeniGender" value="1">Seçebilir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Oto Beğeni Max Gün</label>
                                <input type="number" name="otoBegeniMaxGun" value="" class="form-control" required placeholder="Oto Beğeni Max. Gün">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Aktiflik</label>
                        <div class="checkbox">
                            <label><input type="checkbox" class="checkbox-inline" value="1" name="isActive"> Aktiflik</label>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                İşlem Limitleri
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Beğeni İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukBegeniLimit" value="" class="form-control" required placeholder="Beğeni İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Beğeni Günlük Kalan Limit</label>
                            <input type="number" name="gunlukBegeniLimitLeft" value="" class="form-control" required placeholder="Beğeni İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Reel Beğeni İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukBegeniLimit" value="<?php echo $bayi["gunlukBegeniLimit"]; ?>" class="form-control" required placeholder="Reel Beğeni İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Reel Beğeni Günlük Kalan Limit / Gün</label>
                            <input type="number" name="gunlukBegeniLimit" value="<?php echo $bayi["gunlukBegeniLimit"]; ?>" class="form-control" required placeholder="Reel Beğeni İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Takip İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukTakipLimit" value="" class="form-control" required placeholder="Takip İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Takip Günlük Kalan Limit</label>
                            <input type="number" name="gunlukTakipLimitLeft" value="" class="form-control" required placeholder="Takip İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Yorum İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukYorumLimit" value="" class="form-control" required placeholder="Yorum İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Yorum Günlük Kalan Limit</label>
                            <input type="number" name="gunlukYorumLimitLeft" value="" class="form-control" required placeholder="Yorum İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Yorum Beğeni İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukYorumBegeniLimit" value="" class="form-control" required placeholder="Yorum Beğeni İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Yorum Beğeni Günlük Kalan Limit</label>
                            <input type="number" name="gunlukYorumBegeniLimitLeft" value="" class="form-control" required placeholder="Yorum Begeni İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Canlı Yayın İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukCanliYayinLimit" value="" class="form-control" required placeholder="Canlı Yayın İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Canlı Yayın Günlük Kalan Limit</label>
                            <input type="number" name="gunlukCanliYayinLimitLeft" value="" class="form-control" required placeholder="Canlı Yayın İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Video Görüntülenme İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukVideoLimit" value="" class="form-control" required placeholder="Video Görüntülenme İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Video Görüntülenme Günlük Kalan Limit</label>
                            <input type="number" name="gunlukVideoLimitLeft" value="" class="form-control" required placeholder="Video Görüntülenme İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Reel Video Görüntülenme İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukVideoLimit" value="<?php echo $bayi["gunlukVideoLimit"]; ?>" class="form-control" required placeholder="Reel Video Görüntülenme İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Reel Video Görüntülenme Günlük Kalan Limit</label>
                            <input type="number" name="gunlukVideoLimitLeft" value="<?php echo $bayi["gunlukVideoLimitLeft"]; ?>" class="form-control" required placeholder="Reel Video Görüntülenme İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kaydetme İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukSaveLimit" value="" class="form-control" required placeholder="Kaydetme İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kaydetme Günlük Kalan Limit</label>
                            <input type="number" name="gunlukSaveLimitLeft" value="" class="form-control" required placeholder="Kaydetme İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Story İşlem Limiti / Gün</label>
                            <input type="number" name="gunlukStoryLimit" value="" class="form-control" required placeholder="Story İşlem Limiti">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Story Günlük Kalan Limit</label>
                            <input type="number" name="gunlukStoryLimitLeft" value="" class="form-control" required placeholder="Story İşlem Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Toplam Oto Beğeni Limiti</label>
                            <input type="number" name="toplamOtoBegeniLimit" value="" class="form-control" required placeholder="Oto Beğeni Toplam Limit">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Toplam Oto Beğeni Kalan Limit</label>
                            <input type="number" name="toplamOtoBegeniLimitLeft" value="" class="form-control" required placeholder="Oto Beğeni Kalan Limit">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Bayilik Bitiş Tarihi</label>
                    <input type="text" name="sonaErmeTarihi" value="" class="form-control" required placeholder="Bayilik Bitiş Tarihi örn: <?php echo date("d.m.Y"); ?> 00:00:00">
                </div>
                <div class="form-group">
                    <label>Notlar</label>
                    <textarea style="resize:none" class="form-control" name="notlar" rows="3" placeholder="Notlar"></textarea>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
    </div>
    </form>
</div>