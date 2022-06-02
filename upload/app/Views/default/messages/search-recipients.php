<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    foreach($model["users"] as $item) { ?>
        <a class="list-group-item" href="javascript:void(0);" onclick="addRecipient('<?php echo $item["pk"]; ?>','<?php echo $item["username"]; ?>','<?php echo str_replace("http:","https:",$item["profile_pic_url"]); ?>');"><img class="img-circle" style="max-width: 30px;" src="<?php echo str_replace("http:","https:",$item["profile_pic_url"]); ?>"/> <?php echo $item["username"]; ?>
        </a>
    <?php } ?>