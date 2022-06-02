<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    foreach($model["thread"]["users"] as $user) { ?>
        <a href="/user/<?php echo $user["pk"]; ?>"><img class="img-circle" style="max-width: 30px;" src="<?php echo str_replace("http:","https:",$user["profile_pic_url"]); ?>"/></a> <a href="/user/<?php echo $user["pk"]; ?>"><?php echo $user["username"]; ?></a>
    <?php } ?>