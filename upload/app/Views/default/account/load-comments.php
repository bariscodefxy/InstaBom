<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $model
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $uyelik      = $logonPerson->member;
    $mediaID     = $model["media"]["items"][0]["id"];
?>
<ul class="nav nav-tabs" style="margin-bottom: 15px;">
    <li class="active">
        <a href="#tabComments<?php echo $mediaID; ?>" data-toggle="tab">Yorumlar (<?php echo $model["comments"]["comment_count"]; ?>)</a>
    </li>
    <li>
        <a href="#tabLikers<?php echo $mediaID; ?>" data-toggle="tab">Beğeniler (<?php echo $model["likers"]["user_count"]; ?>)</a>
    </li>
</ul>
<div id="myTabContent<?php echo $mediaID; ?>" class="tab-content">
    <div class="tab-pane fade active in" id="tabComments<?php echo $mediaID; ?>">
        <?php if(isset($model["comments"]["caption"])) { ?>
            <div class="panel panel-primary">
                <div class="panel-body" style="word-wrap:break-word;">
                    <img src="<?php echo str_replace("http:","https:",$model["comments"]["caption"]["user"]["profile_pic_url"]); ?>" class="img-circle" style="max-width: 26px;">
                    <a href="/user/<?php echo $model["comments"]["caption"]["user"]["pk"]; ?>"><strong><?php echo $model["comments"]["caption"]["user"]["username"] ?></strong></a>
                    <?php
                        $desc = preg_replace('/#([^ \/][\w\._üÜğĞşŞıİçÇöÖ]+)/', '<a href="/account/tag/\1">#\1</a>', $model["comments"]["caption"]["text"]);
                        $desc = preg_replace('/@([^ \/][\w\._üÜğĞşŞıİçÇöÖ]+)/', '<a href="/user/\1">@\1</a>', $desc);
                    ?>
                    <p><?php echo $desc; ?></p>
                    <p><i><?php echo date("d.m.Y H:i", $model["comments"]["caption"]["created_at"]); ?></i></p>
                </div>
            </div>
            <?php if($model["media"]["items"][0]["user"]["pk"] == $uyelik["instaID"]) { ?>
                <a href="#modalEditMedia" class="btn btn-block btn-primary" data-toggle="modal" onclick="editMedia('<?php echo $mediaID; ?>');">Açıklama Düzenle</a>
            <?php } ?>
        <?php } ?>
        <hr/>
        <?php
            if(empty($model["comments"]["comments"])) {
                ?>
                <p>Henüz yorum yok..</p>
                <?php
            } else {
                foreach($model["comments"]["comments"] as $comment) {
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <img data-original="<?php echo str_replace("http:","https:",$comment["user"]["profile_pic_url"]); ?>" class="img-circle lazy" style="max-width: 26px;">
                            <a href="/user/<?php echo $comment["user"]["pk"]; ?>"><strong><?php echo $comment["user"]["username"] ?></strong></a>
                        </div>
                        <div class="panel-body" style="word-wrap:break-word;">
                            <?php
                                $comm = preg_replace('/#([^ \/][\w\._üÜğĞşŞıİçÇöÖ]+)/', '<a href="/account/tag/\1">#\1</a>', $comment["text"]);
                                $comm = preg_replace('/@([^ \/][\w\._üÜğĞşŞıİçÇöÖ]+)/', '<a href="/user/\1">@\1</a>', $comm);
                            ?>
                            <p><?php echo $comm; ?></p>
                            <p><i><?php echo date("d.m.Y H:i", $comment["created_at"]); ?></i></p>
                            <?php if($comment["user_id"] == $uyelik["instaID"] || $model["comments"]["caption"]["user_id"] == $uyelik["instaID"]) { ?>
                                <p>
                                    <a href="javascript:void(0);" onclick="$(this).attr('disabled','disabled').html('Siliniyor..');deleteComment('<?php echo $comment["pk"]; ?>','<?php echo $mediaID; ?>','<?php echo $comment["text"]; ?>');" class="btn btn-primary btn-xs">Bu Yorumu Sil</a>
                                </p>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
            }
        ?>
        <hr/>
        <form id="commentForm<?php echo $mediaID; ?>">
            <div class="form-group">
                <label>Senin Yorumun Nedir?</label>
                <textarea class="form-control" name="yorum" rows="2" required></textarea>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-block btn-success" onclick="saveComment('<?php echo $mediaID; ?>');$(this).html('Bekleyin..').attr('disabled','disabled');">Gönder</button>
            </div>
        </form>
    </div>

    <div class="tab-pane fade" id="tabLikers<?php echo $mediaID; ?>">
        <?php if(empty($model["likers"]["users"])) {
            ?>
            <p>Henüz beğeni yok..</p>
            <?php
        } else { ?>
            <table class="table table-bordered table-striped">
                <tbody>
                <?php foreach($model["likers"]["users"] as $user) { ?>
                    <tr>
                        <td>
                            <img data-original="<?php echo str_replace("http:","https:",$user["profile_pic_url"]); ?>" class="img-circle lazy" style="max-width: 30px;">
                            <a href="/user/<?php echo $user["pk"]; ?>"><strong><?php echo $user["username"] ?></strong></a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
        }
        ?>
    </div>
</div>