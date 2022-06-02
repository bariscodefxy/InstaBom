<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Admin Hesap Bilgilerini Düzenleme");
    $logonPerson = $this->get("logonPerson");
    if(!$logonPerson->isLoggedIn()) {
        return;
    }
    $uyelik = $logonPerson->member;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        Admin Hesap Bilgilerini Düzenle
    </div>
    <div class="panel-body">
        <p>Admin bilgilerini değiştirmek yada güncellemek için bu bölümü kullanabilirsin.</p>
        <p class="text-warning">Sadece kullanıcı adınızı değiştirmek istiyorsanız. Eski şifrenizi girmeniz yeterlidir.</p>
        <form method="POST" action="<?php echo Wow::get("project/adminPrefix"); ?>/account">
            <div class="form-group">
                <label class="col-sm-2 control-label">Kullanıcı Adı</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" value="<?php echo $uyelik["username"]; ?>" placeholder="Kullanıcı Adı" name="username" required autocomplete="off"/>
                </div>
            </div>
            <div style="clear:both;"></div>
            <br/>

            <div class="form-group">
                <label class="col-sm-2 control-label">Eski Şifren</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" value="" placeholder="Eski Şifreni girmen gerekiyor" name="oldpass" required/>
                </div>
            </div>
            <div style="clear:both;"></div>
            <br/>

            <div class="form-group">
                <label class="col-sm-2 control-label">Yeni Şifren</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" value="" placeholder="Yeni şifreni girmen gerekiyor" name="newpass"/>
                </div>
            </div>
            <div style="clear:both;"></div>
            <br/>

            <div class="form-group">
                <label class="col-sm-2 control-label">Yeni Şifren Tekrar</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" value="" placeholder="Yeni şifreni tekrar girmen gerekiyor" name="renewpass"/>
                </div>
            </div>

            <div style="clear:both;"></div>
            <br/>
            <div class="text-center">
                <button type="submit" class="btn btn-success">Değişiklikleri Kaydet</button>
            </div>
        </form>
    </div>
</div>
