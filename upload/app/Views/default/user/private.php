<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $accountInfo    = $this->get("accountInfo");
    $userFriendship = $this->get("userFriendship");
    $this->set("title", $accountInfo["user"]["full_name"]);
    $this->renderView("shared/user-header", $accountInfo);
?>
<div class="container">
    <div class="tab-content">
        <div class="tab-pane fade active in">
            <p class="text-danger" style="margin-top:25px;">Bu hesap gizli!
                <?php if($userFriendship["outgoing_request"] == 1) { ?>
                    Kullanıcı isteğinizi kabul edene kadar beklemeniz gerekiyor.
                <?php } else { ?>
                    Hesap detaylarını görüntüleyebilmek için istek göndermelisiniz.
                <?php } ?></p>
        </div>
    </div>
</div>