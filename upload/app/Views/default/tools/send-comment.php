<?php
	/**
	 * @var \Wow\Template\View      $this
	 * @var array                   $media
	 * @var \App\Models\LogonPerson $logonPerson
	 */
	$media       = NULL;
	$logonPerson = $this->get("logonPerson");
	if($this->has("media")){
		$media = $this->get("media");
	}
	$uyelik      = $logonPerson->member;
	$helper      = new \App\Libraries\Helpers();
    $isMobile    = $helper->is_mobile();
?>
    <div class="container">
        <div class="cl10"></div>
        <div class="row">
            <div class="col-sm-8 col-md-9">
                <center>
                <h4 style="margin-top: 0;">Yorum Gönderme Aracı</h4>
                <p>Yorum gönderme aracı ile, dilediğiniz gönderiye, kendi belirlediğiniz adette ve içerikte yorumu anlık olarak gönderebilirsiniz. Gönderilen yorumların tamamı gerçek kullanıcılar tarafındandır.</p>
                <p>Maximum yorum krediniz kadar, yorum gönderebilirsiniz!</p>
                <p>Yorum göndereceğiniz profil gizli olmamalıdır! Gizli profillerin gönderilerine ulaşılamadığından, yorum da gönderilememektedir.</p>
</center>
   <div style="text-align: center;"><em></em><span style="font-size:18px;"><strong> (Reklama Tıkla Bize Yaz +250 Yorum Kredisi Verelim) </strong></span></div> 
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
                        <div <a style="border-radius:20px;"
    class="panel-heading"> <center><i></i> <i <center><i><img alt="Instagram comments" src="/img/chatt.png" style="height: 18px; width: 18px;"></i></i>
                            Yorum Gönder
                        </div>
                        <div class="panel-body">
                            <form method="post" action="?formType=findMediaID" class="form">
                                <div class="form-group">
                                    <center>
                                    <label>Gönderi Url'si</label>
                                    </center>
                                    <input type="text" <a style="border-radius:20px;"
    name="mediaUrl" class="form-control" placeholder="https://www.instagram.com/p/3H0-Yqjo7u/" required>
                                </div>
                                <center><button type="submit" style="border-radius:20px;" class="btn btn-warning animated infinite jello"> Gönderiyi Bul</button></center>

                            </form>
                        </div>
                    </div>
                <?php } elseif($media["items"][0]["user"]["is_private"] == 1) { ?>
                    <hr/>
                    <p class="text-danger">Uppps! Bu gönderiyi paylaşan profil gizli. Gizli profillerin gönderilerine ulaşılamadığından, yorum da gönderilememektedir.</p>
                <?php } elseif(isset($media["items"][0]["comments_disabled"]) && $media["items"][0]["comments_disabled"] == 1) {
                    ?>
                    <p class="text-danger">Uppps! Bu gönderi yorumlara kapalı.</p>
                <?php } else { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading"> <center><i></i> <i <center><i><img alt="Instagram comments" src="/img/chatt.png" style="height: 18px; width: 18px;"></i></i>
                            Yorum Gönder
                        </div>
                        <div  class="panel-body">
						<?php $item = $media["items"][0]; ?>
									<div class="proftool">
										<center><div class="toolpro" style="padding-top:1px;">
											<img style="margin-top: 7px;border-radius: 10px 5px 10px 5px; height:100px;" src="<?php echo $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>" class="img-responsive" style="max-height: 200px;"/>
										</div>
										</center>
									</div>
									<div class="prokredi">
										<div class="kredikt-acik">
											<div class="padder-v"> <span class="block font-bold" id="yorumKrediCount"><?php echo $logonPerson->member["yorumKredi"]; ?></span> <small <?php if($isMobile) { ?>style="font-size: 70%;" <?php } ?> class="text-lt font-bold">Kredi</small> </div>
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
								</div>
								<div class="cl10"></div>
                            <form id="formYorum" class="form">
                                <div class="form-group">
                                    <center>
                                    <label>Yorumlar</label>
                                    <?php
                                        $sampleComments = array(
                                            "Woww. Süper görünüyor :)",
                                            "Gerçekten harikaaaa..",
                                            "Çoook güzeeel.",
                                            "Vayy be.",
                                            "Bayıldım buna.",
                                            "Valla ne desem bilemedim, süper."
                                        );
                                    ?>
                                    <div id="commentList">
                                        <?php foreach($sampleComments as $comment) { ?>
                                            <div class="input-group" style="margin-bottom: 5px;">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" onclick="$(this).parent().parent().remove();"><i class="fa fa-remove"></i></button>
                                    </span>
                                                <input type="text" class="form-control" name="yorum[]" value="<?php echo $comment; ?>">
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <span class="help-block"><a href="javascript:void(0);" onclick="addNewComment();">+ Ekle</a></span>
                                    <span class="help-block">Her kutuya 1 yorum gelecek şekilde yorumları yazınız. Her yorum için 1 yorum krediniz eksilecektir.</span>
                                </div>
                                <input type="hidden" name="mediaID" value="<?php echo $item["id"]; ?>">
                                <input type="hidden" name="mediaCode" value="<?php echo $item["code"]; ?>">
                                                                 <center><button type="button" id="formYorumSubmitButton" style="border-radius:20px;" class="btn btn-warning animated infinite pulse" onclick="sendYorum();"> İşlemi Başlat</button></center>

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
            var countYorum, countYorumMax, clearCommentedIndex;

            function addNewComment() {
                html = '<div class="input-group" style="margin-bottom: 5px;"><span class="input-group-btn"><button class="btn btn-default" type="button" onclick="$(this).parent().parent().remove();"><i class="fa fa-remove"></i></button></span><input type="text" class="form-control" name="yorum[]" value=""></div>';
                $('#commentList').append(html);
            }

            function sendYorum() {
                countYorumMax = 0;
                $("#formYorum input[name='yorum[]']").each(function() {
                    if($.trim($(this).val()) != '') {
                        countYorumMax++;
                    }
                });
                if(countYorumMax === 0) {
                    alert('En az 1 yorum eklemelisin!');
                    return;
                }
                if(countYorumMax > <?php echo $logonPerson->member->yorumKredi; ?>) {
                    return confirm('Girdiğiniz yorum sayısı, yorum kredinizden fazla. İlk <?php echo $logonPerson->member->yorumKredi; ?> tanesi gönderilsin mi?');
                }
                countYorum = 0;
                $('#formYorumSubmitButton').html('<i class="fa fa-spinner fa-spin fa-2x"></i> Gönderimi Başlat');
                $('#formYorum input').attr('readonly', 'readonly');
                $('#formYorum button').attr('disabled', 'disabled');
                $('#userList').html('');
                clearCommentedIndex = 1;
                sendYorumRC();
            }

            function sendYorumRC() {
                $.ajax({type: 'POST', dataType: 'json', url: '?formType=send&clearCommentedIndex=' + clearCommentedIndex, data: $('#formYorum').serialize()}).done(function(data) {
                    clearCommentedIndex = 0;
                    if(data.status == 'error') {
                        $('#userList').prepend('<p class="text-danger">' + data.message + '</p>');
                        sendYorumComplete();
                    }
                    else {
                        for(var i = 0; i < data.users.length; i++) {
                            var user = data.users[i];
                            if(user.status == 'success') {
                                $('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-success">Başarılı</span></p>');
                                countYorum++;
                                $('#yorumKrediCount').html(data.yorumKredi);

                            }
                            else {
                                //$('#userList').prepend('<p><a href="/user/' + user.instaID + '">' + user.userNick + '</a> kullanıcı denendi. Sonuç: <span class="label label-danger">Başarısız</span></p>');
                            }

                        }
                        if(countYorum < countYorumMax) {
                            sendYorumRC();
                        }
                        else {
                            sendYorumComplete();
                        }
                    }
                });
            }

            function sendYorumComplete() {
                $('#formYorumSubmitButton').html('Gönderimi Başlat');
                $('#formYorum input').removeAttr('readonly');
                $('#formYorum button').prop("disabled", false);
                $('#userList').prepend('<p class="text-success">Gönderilen toplam yorum adedi: ' + countYorum + '</p>');
            }
        </script>
    <?php }
    $this->endSection(); ?>