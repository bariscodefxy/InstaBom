<?php
	/**
	 * @var \Wow\Template\View      $this
	 * @var array                   $media
	 */
	$user        = NULL;
	if($this->has("user")){
		$user = $this->get("user");
	}
	$logonPerson = $this->get("logonPerson");
	$uyelik      = $logonPerson->member;
	$helper      = new \App\Libraries\Helpers();
	$isMobile    = $helper->is_mobile();
?>
    <div class="container">
        <div class="cl10"></div>
        <div class="row">
            <div class="col-sm-8 col-md-9">
                <center>
                <h4 style="margin-top: 0;">Takipçi Gönderme Aracı</h4>
                <p>Takipçi gönderme aracı ile, dilediğiniz kullanıcıya, kendi belirlediğiniz adette takipçiyi anlık olarak gönderebilirsiniz. Gönderilen takipçilerin tamamı gerçek kullanıcılardır.</p>
                <p>Maximum takipçi krediniz kadar, takipçi gönderebilirsiniz!</p>
</center>
   <div style="text-align: center;"><em></em><span style="font-size:18px;"><strong> (Reklama Tıkla Bize Yaz +250 Takipçi Kredisi Verelim) </strong></span></div> 
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle"
     style="display:block"
     data-ad-format="fluid"
     data-ad-layout-key="-gw-3+1f-3d+2z"
     data-ad-client="ca-pub-1794874368201737"
     data-ad-slot="8751954348"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
<br></br>
				<?php if(is_null($user)){ ?>
                    <div <a style="border-radius:20px;"
    class="panel panel-default">
                        <div <a style="border-radius:20px;"
    class="panel-heading"> <center><i></i> <i <center><i><img alt="Instagram follower" src="/img/add.png" style="height: 18px; width: 18px;"></i></i>
                            Takipçi Gönder
                        </div>
                        <div class="panel-body">
                            <form method="post" action="?formType=findUserID" class="form">
                                <div class="form-group">
                                    <center>
                                    <label>Kullanıcı Adı</label>
                                    </center>
                                    <input type="text" <a style="border-radius:20px;"
    name="username" class="form-control" placeholder="salim.zorlu61" required>
                                </div>
                                
                             <center><button type="submit" style="border-radius:20px;" class="btn btn-warning animated infinite jello">Kulanıcıyı Bul</button></center>
                            </form>
                        </div>
                    </div> 
                    
				<?php } else { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">  <center><i></i> <i <center><i><img alt="Instagram follower" src="/img/add.png" style="height: 18px; width: 18px;"></i></i>
                            Takipçi Gönder 
                        </div>
 <div class="panel-body">
                            <form id="formTakip" class="form">
							<div class="form-group">
								<div class="proftool">
										<center><div class="toolpro">
											<img src="<?php echo str_replace("http:", "https:",$user["user"]["profile_pic_url"]); ?>"/>
										</div>
										<div class="m-t m-b-xs font-bold text-lt">@<?php echo $user["user"]["username"]; ?></div>
										</center>
									</div>
									<div class="prokredi">
										<div class="kredikt-acik">
											<div class="padder-v"> <span class="block font-bold" id="takipKrediCount"><?php echo $uyelik["takipKredi"]; ?></span> <small <?php if($isMobile) { ?>style="font-size: 70%;" <?php } ?> class="text-lt font-bold">Takipci Kredisi</small> </div>
										</div>
										<div class="kredikt-kapali">
											<div class="padder-v"> <span class="block font-bold"><?php echo $user["user"]["follower_count"]; ?></span> <small <?php if($isMobile) { ?>style="font-size: 70%;" <?php } ?> class="text-lt font-bold">Takipçi</small> </div>
										</div>
										<div class="kredikt-acik">
											<div class="padder-v"> <span class="block font-bold"><?php echo $user["user"]["following_count"]; ?></span> <small <?php if($isMobile) { ?>style="font-size: 70%;" <?php } ?> class="text-lt font-bold">Takip Edilen</small> </div>
										</div>
										<div class="kredikt-kapali">
											<div class="padder-v"> <span class="block font-bold"><?php if($logonPerson->member->isUsable !== 0) { ?>Normal<?php } else { ?><font color="#47fb00">Premium</font><?php } ?></span> <small <?php if($isMobile) { ?>style="font-size: 70%;" <?php } ?> class="text-lt font-bold">Üyelik Türü</small> </div>
										</div>
									</div>
									<br>
                                <div class="form-group" style="position: relative;">
                                    <center>
                                    <label>Takipçi Sayısı</label>
                                    </center>
                                    <input type="text" <a style="border-radius:20px;"
    name="adet" class="form-control" placeholder="10" value="30">
                                </div>
                                <center>
                                <span class="help-block">Max <?php echo $logonPerson->member->takipKredi; ?> takipçi gönderebilirsiniz.</span>
                                </center>
                                <input type="hidden" name="userID" value="<?php echo $user["user"]["pk"]; ?>">
                                <input type="hidden" name="userName" value="<?php echo $user["user"]["username"]; ?>">
                              <center><button type="button" id="formTakipSubmitButton" style="border-radius:20px;" class="btn btn-warning animated infinite pulse" onclick="sendTakip();"> İşlemi Başlat</button></center>
							</div>
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