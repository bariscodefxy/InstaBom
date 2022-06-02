<?php
	/**
	 * @var \Wow\Template\View      $this
	 * @var array                   $media
	 */
	$user        = NULL;
	if($this->has("user")){
		$user = $this->get("user");
	}
?>
<script data-ad-client="ca-pub-3082138989453894" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <div class="container">
        <div class="cl10"></div>
        <div class="row">
            <div class="col-sm-8 col-md-9">
                <center>
                <h4 style="margin-top: 0;">Story İzlenme Gönderim Aracı</h4>
                                <p><strong><span style="color:#000000;">STORY İZLENME GÖNDERİMİ HAKKINDA :</span><br><span style="color:#4b0082;"> </span><span style="color:#b22222;">Artık </span><span style="color:#000000;">kullanıcı adına </span><span style="color:#b22222;">story izlenme gönderebileceksiniz. Sizin için 7/24 çabalıyoruz, uğraşıyoruz.. <span style="color:#000000;">Sevgiler...</span></span></strong></p>
</center>
				<?php if(is_null($user)){ ?>
                    <div style="border-radius:20px;" class="panel panel-default">
                       
                        <div style="border-radius:20px;" class="panel-heading">
                             <center>
                            <b>Story İzlenme Gönder</b> ( <b><span style="color:#b22222;">Kullanıcı Adına.</span></b> )
                          
                        </div>
                     </center>
                        <div class="panel-body">
                            <form method="post" action="?formType=findUserID" class="form">
                                <div class="form-group">
                                    <center>
                                    <label>Kullanıcı Adı</label>
                                    </center>
                                    <input type="text" name="username" style="border-radius:20px;" class="form-control" placeholder="salim.zorlu61" required>
                                </div>
                                <center>
                                <button style="border-radius:20px;" type="submit" class="btn btn-success">Kullanıcının Storysini Bul</button>
                                </center>
                            </form>
                        </div>
                    </div>
				<?php } else { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Story İzlenme Gönder
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-danger hidden-xs" style="max-width:600px; align:center;">Uygulamamızdan <span style="color:#33f600;">sisteme giriş yapmadığınız </span>bir hesapla girerseniz krediniz <strong><span style="color:#33f600;">200 </span></strong>olacaktır.<br />
                        Uygulamamızı indirmek için: <strong><a href="https://efsanetakipci.com/yekomedya-force-login.apk"><span style="color:#33f600;">TIKLA</span></a></strong></div>
                        <font color="#b22222">DUYURU : </font><font color="black">Sistem 100 krediyle 100 takipçi atmaktadır.</font>
                            <p>
                            <form id="formTakip" class="form">
                                <div class="form-group">
                                    <label>Kullanıcı:</label>
                                    <img src="<?php echo str_replace("http:","https:",$user["user"]["profile_pic_url"]); ?>" class="img-responsive"/>
                                </div>
                                <div class="form-group">
                                    <label>Gönderilecek Story İzlenme Sayısı:</label>
                                    <input type="text" name="adet" class="form-control" placeholder="500" value="500">
                                </div>
                                <input type="hidden" name="userID" value="<?php echo $user["user"]["pk"]; ?>">
                                <input type="hidden" name="userName" value="<?php echo $user["user"]["username"]; ?>">
                                <button style="border-radius:20px;" type="button" id="formTakipSubmitButton" class="btn btn-success" onclick="sendTakip();">Gönderimi Başlat</button>
                            </form>
                            <div class="cl10"></div>
                            <div id="userList"></div>
                        </div>
                    </div>
				<?php } ?>
            </div>
            <div class="col-sm-4 col-md-3">
				<?php $this->renderView("tools/sidebar"); ?>
            </div>
        </div>
    </div>
<?php $this->section("section_scripts");
	$this->parent();
	if( ! is_null($user)){ ?>
        <script type="text/javascript">
            var countTakip, countTakipMax;

            function sendTakip() {
                countTakip    = 0;
                countTakipMax = parseInt($('#formTakip input[name=adet]').val());

                if(isNaN(countTakipMax) || countTakipMax <= 0) {
                    alert('Story izlenme adedi girin!');
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
                                $('#storyKrediCount').html(data.storyKredi);

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
                $('#userList').prepend('<p class="text-success">Storyi izleyen toplam kullanıcı adedi: ' + countTakip + '</p>');
            }
        </script>
	<?php }
	$this->endSection(); ?>