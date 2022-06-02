<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $media
     */
    $user = NULL;
    if($this->has("user")) {
        $user = $this->get("user");
    }
?>
    <div class="container">
        <div class="cl10"></div>
        <div class="row">
            <div class="col-sm-8 col-md-9">
                <h4 style="margin-top: 0;">Takipçi Gönderme Aracı</h4>
                <p>Takipçi gönderme aracı ile, dilediğiniz kullanıcıya, kendi belirlediğiniz adette takipçiyi anlık olarak gönderebilirsiniz. Gönderilen takipçilerin tamamı gerçek kullanıcılardır.</p>
                <p>Maximum takipçi krediniz kadar, takipçi gönderebilirsiniz!</p>

                <?php if(is_null($user)) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Takipçi Gönder
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Takipçi Gönder
                        </div>
                        <div class="panel-body">
                            <form id="formTakip" class="form">
                                <div class="form-group">
                                    <label>Kullanıcı:</label>
                                    <img src="<?php echo str_replace("http:", "https:",$user["user"]["profile_pic_url"]); ?>" class="img-responsive"/>
                                </div>
                                <div class="form-group">
                                    <label>Takipçi Sayısı:</label>
                                    <input type="text" name="adet" class="form-control" placeholder="10" value="10">
                                </div>
                                <input type="hidden" name="userID" value="<?php echo $user["user"]["pk"]; ?>">
                                <input type="hidden" name="userName" value="<?php echo $user["user"]["username"]; ?>">
                                <button type="button" id="formTakipSubmitButton" class="btn btn-success" onclick="sendTakip();">Gönderimi Başlat</button>
                            </form>
                            <div class="cl10"></div>
                            <div id="userList"></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-sm-4 col-md-3">
                <?php $this->renderView("bayi-tx/sidebar"); ?>
            </div>
        </div>
    </div>
<?php $this->section("section_scripts");
    $this->parent();
    if(!is_null($user)) { ?>
        <script type="text/javascript">
            var countTakip, countTakipMax;

            function sendTakip() {
                countTakip    = 0;
                countTakipMax = parseInt($('#formTakip input[name=adet]').val());

                if(isNaN(countTakipMax) || countTakipMax <= 0) {
                    alert('Takipçi adedi girin!');
                    return false;
                }

                $('#formTakipSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Gönderimi Başlat');
                $('#formTakip input').attr('readonly', 'readonly');
                $('#formTakip button').attr('disabled', 'disabled');
                $('#userList').html('');
                sendTakipRC();
            }

            function sendTakipRC() {
                $.ajax({type: 'POST', dataType: 'json', url: '?formType=send', data: $('#formTakip').serialize()}).done(function(data) {


                    if(data.status == 'error') {
                        $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                        sendTakipComplete();
                    }
                    else {
                        for(var i = 0; i < data.users.length; i++) {
                            var user = data.users[i];
                            if(user.status == 'success') {
                                $('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-success">Başarılı</span></p>');
                                countTakip++;
                                $('#formTakip input[name=adet]').val(countTakipMax - countTakip);
                                $('#takipKrediCount').html(data.takipKredi);

                            }
                            else {
                                //$('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-danger">Başarısız</span></p>');
                            }
                        }
                        if(countTakip < countTakipMax) {
                            sendTakipRC();
                        }
                        else {
                            sendTakipComplete();
                        }
                    }
                });
            }

            function sendTakipComplete() {
                $('#formTakipSubmitButton').html('Gönderimi Başlat');
                $('#formTakip input').removeAttr('readonly');
                $('#formTakip button').prop("disabled", false);
                $('#formTakip input[name=adet]').val('10');
                $('#userList').prepend('<p class="text-success">Takip eden toplam kullanıcı adedi: ' + countTakip + '</p>');
            }
        </script>
    <?php }
    $this->endSection(); ?>