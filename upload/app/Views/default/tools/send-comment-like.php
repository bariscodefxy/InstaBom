<?php

    /**

     * @var \Wow\Template\View      $this

     * @var array                   $media

     * @var \App\Models\LogonPerson $logonPerson

     */

    $logonPerson = $this->get("logonPerson");

    $media       = NULL;

    if($this->has("media")) {

        $media = $this->get("media");

    }

    if($this->has("comment")) {

        $comment = $this->get("comment");

    }

?>
    <div class="container">

        <div class="cl10"></div>

        <div class="row">

            <div class="col-sm-8 col-md-9">

<center>
    <h4>Yoruma Beğeni Gönderme Aracı</h4>
</center>
<?php if(is_null($media)) { ?>
<center>
    <p>Yorum beğeni gönderme aracı ile, dilediğiniz yoruma, kendi belirlediğiniz adette beğeni gönderebilirsiniz. Bu beğeniler sayesinde gönderinin yorumlarını inceleyen kişiler en üstte sizin yorumunuz görecektir. Yorumun en üste çıkması yarım saat sürmektedir.</p>

    <p>Yoruma beğeni göndereceğiniz gönderiyi paylaşan kişinin profili gizli olmamalıdır! Gizli profillerin gönderilerine ulaşılamadığından, yoruma beğeni de gönderilememektedir.</p>
</center>
    <div style="border-radius:20px;" class="panel panel-default">

        <div style="border-radius:20px;" class="panel-heading">
<center>
            Yoruma Beğeni Gönder
</center>
        </div>

        <div class="panel-body">

            <form method="post" action="?formType=findMediaID" class="form">

                <div class="form-group">
<center>
                    <label>Gönderi Url'si</label>
</center>
                    <input type="text" name="mediaUrl" style="border-radius:20px;" class="form-control" placeholder="https://www.instagram.com/p/3H0-Yqjo7u/">

                </div>

                <div class="form-group">
<center>
                    <label>Yorumu Yapan Kullanıcı Adı</label>
</center>
                    <input type="text" name="username" style="border-radius:20px;" class="form-control" placeholder="salim.zorlu61" required>

                </div>
<center>
                <button type="submit" style="border-radius:20px;" class="btn btn-success">Yorumu Bul</button>
</center>
            </form>

        </div>

    </div>
	

<?php } elseif($media["items"][0]["user"]["is_private"] == 1) { ?>

    <hr/>

    <p class="text-danger">Uppps! Bu gönderiyi paylaşan profil gizli. Gizli profillerin gönderilerine ulaşılamadığından, yorum da gönderilememektedir.</p>

<?php } elseif(isset($media["items"][0]["comments_disabled"]) && $media["items"][0]["comments_disabled"] == 1) { ?>

    <hr/>

    <p class="text-danger">Uppps! Bu gönderi yorumlara kapalı.</p>

    <?php

} else { ?>


    <div class="panel panel-default">

        <div class="panel-heading">

            Yorum Beğenisi Gönder

        </div>

        <div class="panel-body">

            <form id="formBegeni" class="form">

                <div class="form-group">

                    <label>Gönderi:</label>

                    <?php $item = $media["items"][0]; ?>

                    <img src="<?php echo $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>" class="img-responsive" style="max-height: 200px;"/>

                </div>



                <div class="form-group">

                    <label>Yorum:</label>

                    <p><?php echo $comment["comment"]; ?></p>

                </div>

                <?php if($logonPerson->member->yorumBegeniGender === 1) { ?>

                    <div class="form-group">

                        <label>Cinsiyet:</label>

                        <select name="gender" class="form-control">

                            <option value="0">Karışık</option>

                            <option value="1">Erkek</option>

                            <option value="2">Bayan</option>

                        </select>

                    </div>

                <?php } ?>

                <div class="form-group">

                    <label>Yorum Beğeni Sayısı:</label>

                    <input type="text" name="adet" class="form-control" placeholder="10" value="10">

                    <span class="help-block">Max <?php echo $logonPerson->member->yorumBegeniMaxKredi; ?> yorum beğenisi gönderebilirsiniz.</span>

                </div>



                <input type="hidden" name="mediaID" value="<?php echo $item["id"]; ?>">

                <input type="hidden" name="yorumID" value="<?php echo $comment["commentID"]; ?>">

                <input type="hidden" name="yorumText" value="<?php echo $comment["comment"]; ?>">

                <input type="hidden" name="mediaCode" value="<?php echo $item["code"]; ?>">

                <input type="hidden" name="userID" value="<?php echo $item["user"]["pk"]; ?>">

                <input type="hidden" name="userName" value="<?php echo $item["user"]["username"]; ?>">

                <input type="hidden" name="imageUrl" value="<?php echo $item["media_type"] == 8 ? str_replace("http:", "https:", $item["carousel_media"][0]["image_versions2"]["candidates"][0]["url"]) : str_replace("http:", "https:", $item["image_versions2"]["candidates"][0]["url"]); ?>">

                <input type="hidden" name="_method" value="POST">

                <button type="button" id="formBegeniSubmitButton" class="btn btn-success" onclick="sendBegeni();">Gönderimi Başlat</button>

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