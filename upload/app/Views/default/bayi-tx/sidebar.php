<?php
    /**
     * @var \Wow\Template\View $this
     */
    $bulkTasks      = array();
    $bulkTasks[]    = array(
        "link"   => "/bayi-tx/send-like",
        "text"   => "Beğeni Gönder",
        "action" => "SendLike",
        "icon"   => "fa fa-heart"
    );
    $bulkTasks[]    = array(
        "link"   => "/bayi-tx/send-follower",
        "text"   => "Takipçi Gönder",
        "action" => "SendFollower",
        "icon"   => "fa fa-user-plus"
    );
    $bulkTasks[]    = array(
        "link"   => "/bayi-tx/send-comment",
        "text"   => "Yorum Gönder",
        "action" => "SendComment",
        "icon"   => "fa fa-comment"
    );
?>
<div class="panel panel-default" style="border-radius:40px;">
    <div class="panel-heading" style="border-radius:25px;">Toplu İşlemler</div>
    <div class="panel-body" style="border-radius:10px;" style="padding: 0;">
        <div class="list-group" style="border-radius:40px;" style="margin-bottom: 0;">
            <?php foreach($bulkTasks as $menu) { ?>
                <a href="<?php echo $menu["link"]; ?>" class="list-group-item<?php echo $this->route->params["action"] == $menu["action"] ? ' active' : ''; ?>">
                    <i class="<?php echo $menu["icon"]; ?>"></i> <?php echo $menu["text"]; ?>
                </a>
            <?php } ?>
        </div>
    </div>
</div>
