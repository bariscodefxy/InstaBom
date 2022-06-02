<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "İşlemler");
?>
<div class="panel panel-default">
    <div class="panel-heading">
        User:Pass Aktarma
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label>User:Pass Listesi</label>
            <textarea id="userpassList" class="form-control" rows="10"></textarea>
            <span class="help-block">username:password'leri her satıra 1 tane gelecek şekilde alt alta yazın. Eğer girdiğiniz kullanıcıların cihaz idleri de mevcutsa username:password:deviceid şeklinde giriş yapabilirsiniz. Eğer gireceğiniz kullanıcıların sessionid'leri de mevcutsa bu sefer de username:password:deviceid:sessionid şeklinde giriş yapabilirsiniz.</span>
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-primary" id="btnAddUserPass" onclick="addUserPass();">Aktar</button>
        </div>
        <div id="listUserPass"></div>
    </div>
</div>


<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    var listAddUserPass, countUserPass;

    function addUserPass() {
        if($('#userpassList').val() == '') {
            alert('Liste boş!');
            return;
        }
        $('#btnAddUserPass').attr('disabled', 'disabled').html('<i class="fa fa-spinner fa-spin fa-3x"></i> AKTARILIYOR..');
        countUserPass   = 0;
        listAddUserPass = $.trim($('#userpassList').val()).split(/\r|\n/);
        $('#listUserPass').html('');
        $('#listUserPass').prepend('<p class="text-primary">' + listAddUserPass.length + ' adet kullanıcı için aktarım başlatıldı.</p>');
        addUserPassRC();
    }

    function addUserPassRC() {
        if(listAddUserPass.length < 1) {
            return addUserPassComplete();
        }
        $user = listAddUserPass[0];
        listAddUserPass.splice(0, 1);
        if($user.length > 0) {
            $.ajax({type: 'POST', dataType: 'json', url: '?formType=addUserPass', data: 'userpass=' + encodeURIComponent($user)}).done(function(data) {
                if(data.status == 'success') {
                    $('#listUserPass').prepend('<p><a href="/user/' + data.instaID + '">' + data.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-success">' + data.message + '</span></p>');
                    countUserPass++;
                } else {
                    $('#listUserPass').prepend('<p><a href="/user/' + data.instaID + '">' + data.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-danger">' + data.message + '</span></p>');
                }
                addUserPassRC();
            });
        } else {
            addUserPassRC();
        }
    }

    function addUserPassComplete() {
        $('#btnAddUserPass').prop("disabled", false).html('AKTAR');
        $('#listUserPass').prepend('<p class="text-success">Aktarım tamamlandı. Yeni eklenen kullanıcı sayısı: ' + countUserPass + '</p>');
    }
</script>
<?php $this->endSection(); ?>