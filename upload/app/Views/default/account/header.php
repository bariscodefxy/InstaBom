<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $model
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $userInfo    = $this->get("accountInfo")["user"];
?>
<div class="cl10"></div>
<div class="container">
    <ul class="nav nav-tabs nav-justified nav-tabs-justified" style="margin-bottom: 15px;">
        <li<?php if($this->route->params["action"] == "Settings") { ?> class="active"<?php } ?>>
            <a href="/account/settings">Hesap Ayarları</a></li>
        <li<?php if($this->route->params["action"] == "Liked") { ?> class="active"<?php } ?>>
            <a href="/account/liked">Beğendiğim Gönderiler</a></li>
    </ul>
</div>