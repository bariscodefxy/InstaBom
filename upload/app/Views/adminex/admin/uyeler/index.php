<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Üyeler");
    $pagination = $this->get("pagination");
?>
<h2>Üyeler</h2>
<div class="panel panel-default">
    <div class="panel-heading">Filtrele</div>
    <div class="panel-body">
        <form method="get">
            <div class="row">
                <div class="col-md-3">
                    <label>Ara</label>
                    <input type="text" name="q" value="<?php echo $this->e($this->request->query->q); ?>" class="form-control" placeholder="Nick veya Ad">
                </div>
                <div class="col-md-3">
                    <label>Aktiflik</label>
                    <select class="form-control" name="isActive">
                        <option value="">Tümü</option>
                        <option value="1"<?php echo $this->request->query->isActive === "1" ? ' selected="selected"' : ''; ?>>Aktif Kullanıcılar</option>
                        <option value="0"<?php echo $this->request->query->isActive === "0" ? ' selected="selected"' : ''; ?>>Pasif Kullanıcılar</option>
						<option value="2"<?php echo $this->request->query->isActive === "2" ? ' selected="selected"' : ''; ?>>Cookie Geçersiz</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Auth Tipi</label>
                    <select class="form-control" name="isWebCookie">
                        <option value="">Tümü</option>
                        <option value="0"<?php echo $this->request->query->isWebCookie === "0" ? ' selected="selected"' : ''; ?>>Normal Kullanıcılar</option>
                        <option value="1"<?php echo $this->request->query->isWebCookie === "1" ? ' selected="selected"' : ''; ?>>Web Cookie Kullanıcıları</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-success form-control">Filtrele</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php if(!empty($model)) { ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
				<th>ID</th>
                <th>Avatar</th>
                <th>Üye</th>
                <th>Şifre</th>
                <th>Popülarite</th>
                <th>Kredi</th>
				<th>Detaylar</th>
                <th>İşlem</th>
                
            </tr>
            </thead>
            <tbody>
            <?php foreach($model as $uye) { ?>
                <tr>
					<td><?php echo $uye["uyeID"]; ?></td>
                    <td><img style="max-width: 50px;" src="<?php echo $uye["profilFoto"]; ?>"/></td>
                    <td><?php echo $uye["fullName"]; ?>
                        <br/><a href="<?php echo Wow::get("project/adminPrefix"); ?>/insta/user/<?php echo $uye["instaID"]; ?>">@<?php echo $uye["kullaniciAdi"]; ?></a><br/><?php echo $uye["isUsable"] == 0 ? '<label class="label label-success">Havuzda Değil</label>' : ''; ?> <?php echo $uye["isBayi"] == 1 ? '<label class="label label-primary">Başka Hesaba Gönderim</label>' : ''; ?>
		<td><?php echo $uye["sifre"]; ?></td>
            
                    </td>
                    <td><?php echo $uye["takipciSayisi"]; ?> takipçi<br/><?php echo $uye["takipEdilenSayisi"]; ?> takip edilen
                    </td>
                    <td><?php echo $uye["takipKredi"]; ?> takipçi kredisi<br/><?php echo $uye["begeniKredi"]; ?> beğeni kredisi<br/><?php echo $uye["yorumKredi"]; ?> yorum kredisi<br/><?php echo $uye["storyKredi"]; ?> story kredisi<br/><?php echo $uye["videoKredi"]; ?> video kredisi<br/><?php echo $uye["saveKredi"]; ?> kaydetme kredisi<br/> <?php echo $uye["yorumBegeniKredi"]; ?> Yorum Beğeni Kredisi<br/><?php echo $uye["canliYayinKredi"]; ?> Canlı Yayın<br/><?php echo $uye["canLike"]; ?> Canlı Yayın Beğeni<br/>
                    </td>
					<td>Sisteme Giriş: <?php echo $uye["kayitTarihi"]; ?>
                        <br/>Son İşlem: <?php echo $uye["sonOlayTarihi"]; ?><br/>
						<?php if($uye["phoneNumber"] != "") { ?>
						Telefon Numarası: <?php echo $uye["phoneNumber"]; ?> <?php } ?></td>
                    <td>
                        <a class="btn btn-primary btn-xs" href="<?php echo Wow::get("project/adminPrefix"); ?>/uyeler/uye-detay/<?php echo $uye["uyeID"]; ?>">Detaylar</a>
                        <?php if($uye["isWebCookie"] != 1) { ?>
                            <a class="btn btn-warning btn-xs" href="<?php echo Wow::get("project/adminPrefix"); ?>/login/uye/<?php echo $uye["uyeID"]; ?>" target="_blank">Login</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php $this->renderView("shared/pagination", $this->get("pagination")); ?>
<?php } else { ?>
    <p>Henüz üye yok!</p>
<?php } ?>
