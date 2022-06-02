<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $messages = array_reverse($model["thread"]["items"]);
    $users    = array();
    foreach($model["thread"]["users"] as $aUser) {
        $users[$aUser["pk"]] = $aUser;
    }

    foreach($messages as $item) {
        $user = isset($users[$item["user_id"]]) ? $users[$item["user_id"]] : FALSE;
        ?>
        <div class="talk-bubble round border<?php echo $user ? ' pull-left tri-right left-in' : ' pull-right tri-right right-in'; ?>">
            <?php if($user) { ?>
                <a href="/user/<?php echo $user["pk"]; ?>"><img class="img-circle profile-img" src="<?php echo $user["profile_pic_url"]; ?>"></a>
            <?php } ?>
            <div class="talktext">
                <p><?php if($user) { ?>
                        <a href="/user/<?php echo $user["pk"]; ?>"><strong><?php echo $user["username"]; ?></strong></a><br/>
                    <?php } ?>
                    <?php
                        switch($item["item_type"]) {
                            case "text":
                                $desc = preg_replace('/#([^ \/][\w\._üÜğĞşŞıİçÇöÖ]+)/', '<a href="/account/tag/\1">#\1</a>', $item["text"]);
                                $desc = preg_replace('/@([^ \/][\w\._üÜğĞşŞıİçÇöÖ]+)/', '<a href="/user/\1">@\1</a>', $desc);
                                echo $desc;
                                break;
                            case "like":
                                echo '<i class="fa fa-heart text-danger"></i>';
                                break;
                            case "media":
                                echo '<img src="' . str_replace("http:","https:",$item["media"]["image_versions2"]["candidates"][0]["url"]) . '" class="img-responsive" />';
                                break;
                            case "media_share":
                                echo '<img src="' . str_replace("http:","https:",$item["media_share"]["image_versions2"]["candidates"][0]["url"]) . '" class="img-responsive" />';
                                break;
                                break;
                            default:
                                echo $item["item_type"];
                        }
                    ?>
                </p>
            </div>
        </div>
    <?php } ?>
<div class="clearfix"></div>