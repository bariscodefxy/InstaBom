
<?php
	/**
	 * @var \Wow\Template\View $this
	 * @var array              $media
	 */
	$media = NULL;
	if($this->has("media")) {
		$media = $this->get("media");
	}
	$logonPerson  = $this->get("logonPerson");
    $uyelik      = $logonPerson->member;
	$helper      = new \App\Libraries\Helpers();
    $isMobile    = $helper->is_mobile();
?>
    <div class="container">
        <div class="cl10"></div>
        <div class="row">
            <div class="col-sm-8 col-md-9">
                <center>
                <h4 style="margin-top: 0;">Beğeni Gönderme Aracı</h4>
                <p>Beğeni gönderme aracı ile, dilediğiniz gönderiye, kendi belirlediğiniz adette beğeniyi anlık olarak gönderebilirsiniz. Gönderilen beğenilerin tamamı gerçek kullanıcılardır.</p>
                <p>Maximum beğeni krediniz kadar, beğeni gönderebilirsiniz!</p>
                <p>Beğeni göndereceğiniz profil gizli olmamalıdır! Gizli profillerin gönderilerine ulaşılamadığından, beğeni de gönderilememektedir.</p>
                </center>
                 <div style="text-align: center;"><em></em><span style="font-size:18px;"><strong> (Reklama Tıkla Bize Yaz +250 Beğeni Kredisi Verelim) </strong></span></div>
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
				<?php if(is_null($media)) { ?>
                    <div <a style="border-radius:20px;"
    class="panel panel-default">
                        <div <a style="border-radius:20px;" class="panel-heading"> <center><i><center><i><img alt="Instagram like" src="/img/heartt.png" style="height: 18px; width: 18px;"></i></i>
                            Beğeni Gönder
                        </div>
                        <div class="panel-body">
                            <form method="post" action="?formType=findMediaID" class="form">
                                <div class="form-group">
                                    <center>
                                    <label>Gönderi Url'si</label>
                                    </center>
                                    <input type="text" <a style="border-radius:20px;" name="mediaUrl" class="form-control" placeholder="https://www.instagram.com/p/3H0-Yqjo7u/" required>
                                </div>
                               <center><button type="submit" style="border-radius:20px;" class="btn btn-warning animated infinite jello"> Gönderiyi Bul</button></center>
                               <br></br>
          
                            </form>
                        </div>
                    </div>
        
				<?php } elseif($media["items"][0]["user"]["is_private"] == 1) { ?>
                    <hr/>
                    <p class="text-danger">Uppps! Bu gönderiyi paylaşan profil gizli. Gizli profillerin gönderilerine ulaşılamadığından, beğeni de gönderilememektedir.</p>
				<?php } else { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading"> <center><i><center><i><img alt="Instagram like" src="/img/heartt.png" style="height: 18px; width: 18px;"></i></i>
                            Beğeni Gönder
                        </div>
                        <div class="panel-body">
						<div class="proftool">
										<center><div class="toolpro" style="padding-top:1px;">
											<?php $item = $media["items"][0]; ?>
											<img style="margin-top: 7px;border-radius: 10px 5px 10px 5px; height:100px;" src="<?php echo $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>" class="img-responsive" style="max-height: 200px;"/>
										</div>
										</center>
									</div>
									<div class="prokredi">
										<div class="kredikt-acik">
											<div class="padder-v"> <span class="block font-bold" id="begeniKrediCount"><?php echo $logonPerson->member["begeniKredi"]; ?></span> <small <?php if($isMobile) { ?>style="font-size: 70%;" <?php } ?> class="text-lt font-bold">Kredi</small> </div>
										</div>
										<div class="kredikt-kapali">
											<div class="padder-v"> <span class="block font-bold"><?php echo $item["like_count"]; ?></span> <small <?php if($isMobile) { ?>style="font-size: 70%;" <?php } ?> class="text-lt font-bold">Beğeni Sayısı</small> </div>
										</div>
										<div class="kredikt-acik">
											<div class="padder-v"> <span class="block font-bold"><?php echo $item["comment_count"]; ?></span> <small <?php if($isMobile) { ?>style="font-size: 70%;" <?php } ?> class="text-lt font-bold">Yorum Sayısı</small> </div>
										</div>
										<div class="kredikt-kapali">
											<div class="padder-v"> <span class="block font-bold"><?php if($logonPerson->member->isUsable !== 0) { ?>Normal<?php } else { ?><font color="#47fb00">Premium</font><?php } ?></span> <small <?php if($isMobile) { ?>style="font-size: 70%;" <?php } ?> class="text-lt font-bold">Üyelik Türü</small> </div>
										</div>
									</div><br>
                            <form id="formBegeni" class="form">
                                <div class="form-group">
                                    <center>
                                    <label>Beğeni Sayısı</label>
                                    </center>
                                    <input type="text" <a style="border-radius:20px;"
    name="adet" class="form-control" placeholder="10" value="10">
                                </div>
                                <input type="hidden" name="mediaID" value="<?php echo $item["id"]; ?>">
                                <input type="hidden" name="mediaCode" value="<?php echo $item["code"]; ?>">
                                <center><button type="button" id="formBegeniSubmitButton" style="border-radius:20px;" class="btn btn-warning animated infinite pulse" onclick="sendBegeni();"> İşlemi Başlat</button></center>

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
	if(!is_null($media) && $media["items"][0]["user"]["is_private"] != 1) { ?>
        <script type="text/javascript">
            var countBegeni, countBegeniMax;

            function sendBegeni() {
                countBegeni    = 0;
                countBegeniMax = parseInt($('#formBegeni input[name=adet]').val());
                if(isNaN(countBegeniMax) || countBegeniMax <= 0) {
                    alert('Beğeni adedi girin!');
                    return false;
                }
                $('#formBegeniSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Gönderimi Başlat');
                $('#formBegeni input').attr('readonly', 'readonly');
                $('#formBegeni button').attr('disabled', 'disabled');
                $('#userList').html('');
                sendBegeniRC();
            }

            function sendBegeniRC() {
                $.ajax({type: 'POST', dataType: 'json', url: '?formType=send', data: $('#formBegeni').serialize()}).done(function(data) {
                    if(data.status == 'error') {
                        $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                        sendBegeniComplete();
                    }
                    else {
                        for(var i = 0; i < data.users.length; i++) {
                            var user = data.users[i];
                            if(user.status == 'success') {
                                $('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-success">Başarılı</span></p>');
                                countBegeni++;
                                $('#formBegeni input[name=adet]').val(countBegeniMax - countBegeni);
                                $('#begeniKrediCount').html(data.begeniKredi);

                            }
                            else {
                                //$('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-danger">Başarısız</span></p>');
                            }
                        }
                        if(countBegeni < countBegeniMax) {
                            sendBegeniRC();
                        }
                        else {
                            sendBegeniComplete();
                        }
                    }
                });
            }

            function sendBegeniComplete() {
                $('#formBegeniSubmitButton').html('Gönderimi Başlat');
                $('#formBegeni input').removeAttr('readonly');
                $('#formBegeni button').prop("disabled", false);
                $('#formBegeni input[name=adet]').val('10');
                $('#userList').prepend('<p class="text-success">Gönderilen toplam beğeni adedi: ' + countBegeni + '</p>');
            }
        </script>
	<?php }
	$this->endSection(); ?>