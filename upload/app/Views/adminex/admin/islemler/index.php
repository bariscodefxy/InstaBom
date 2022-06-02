<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Pasif Kullanıcı Temizleme");
?>
<div class="panel panel-default">
    <div class="panel-heading">
        Pasif Kullanıcıları Temizleme
    </div>
    <div class="panel-body">
        <p>Pasif Kullanıcılar, artık cookie'leri çalışmayan veya kullanıcı adı & şifrelerini değiştirmiş olan kullanıcıları ifade eder. Bu kullanıcılar sistemde tutmanın kimseye bir yararı yoktur. Temizlemek en iyisi olacaktır! Aynı zamanda pasif kullanıcıları silerek, sql sorgularının hızlanmasına katkıda bulunabilirsiniz.</p>
        <?php if($model["countPassiveUsers"] == 0) { ?>
            <p class="text-success">Tebrikler, sistemde hiç pasif kullanıcı yok.</p>
        <?php } else { ?>
            <p class="text-danger">Tespit edilen pasif kullanıcı sayısı:
                <strong><?php echo $model["countPassiveUsers"]; ?> adet</strong></p>
            <p>
                <a href="javascript:void(0);" id="btnRemovePassiveUsers" onclick="removePassiveUsers();" class="btn btn-primary">TEMİZLE</a>
            </p>
        <?php } ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        NA Dönen Kullanıcıları Temizleme
    </div>
    <div class="panel-body">
        <p>NA Kullanıcılar, anlık olarak kontrol edilemeyen, instagram tarafından kapatılan yada erişim engeli getirilen hesapları ifade eder. Bu hesapları aşağıdaki listeden kopyalayarak user:pass aktarım bölümünden son kez giriş yapmalarını deneyebilirsiniz.</p>
        <?php if($model["countNaUsers"] == 0) { ?>
            <p class="text-success">Tebrikler, sistemde hiç NA kullanıcı yok.</p>
        <?php } else { ?>
            <p class="text-danger">Tespit edilen NA kullanıcı sayısı:
                <strong><?php echo $model["countNaUsers"]; ?> adet</strong></p>
            <p>
                <a href="javascript:void(0);" id="btnRemoveNAUsers" onclick="removeNAUsers();" class="btn btn-primary">TEMİZLE</a>
            </p>
            <br/>
            <hr/><br/>
        <?php } ?>
        <?php if($model["countPassiveUsers"] > 0 || $model["countNaUsers"] > 0) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    NA ve Pasif Dönen List
                </div>
                <div class="panel-body">
                    <p>Bu listeyi kopyalayarak
                        <a href="/admin/islemler/add-user-pass"> User:Pass Aktarma</a> bölümünden tekrar login olmalarını sağlayabilirsiniz. Eğer şifrelerini değiştirmedilerse yeniden login olacaklardır. Bu işlemi yaptıktan sonra bu bölüme gelip NA'daki userları da Pasif'deki userları da silebilirsiniz çünkü onların patladığından artık eminiz.
                    </p>
                    <textarea class="form-control" rows="5"><?php foreach($model["listNaUsers"] AS $user) {
                            echo $user["userpass"] . "\n";
                        } ?></textarea>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    function removePassiveUsers() {
        $('#btnRemovePassiveUsers').attr('disabled', 'disabled').html('<i class="fa fa-spinner fa-spin fa-3x"></i> TEMİZLENİYOR..');
        $.ajax({type: 'POST', dataType: 'json', url: '?formType=removePassiveUsers'}).done(function(data) {
            window.location.href = window.location.href;
        });
    }

    function removeNAUsers() {
        $('#btnRemoveNAUsers').attr('disabled', 'disabled').html('<i class="fa fa-spinner fa-spin fa-3x"></i> TEMİZLENİYOR..');
        $.ajax({type: 'POST', dataType: 'json', url: '?formType=removeNAUsers'}).done(function(data) {
            window.location.href = window.location.href;
        });
    }
</script>
<?php $this->endSection(); ?>