<?php
    /**
     * @var \Wow\Template\View      $this
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    if(!$logonPerson->isLoggedIn()) {
        return;
    }
?>
<div class="bg-accountbar">
    <div class="container">
        <ul class="nav nav-justified nav-accountbar">
            <li<?php echo $this->route->params["controller"] == "Account" && $this->route->params["action"] == "Index" ? ' class="active"' : ''; ?>>
                <a href="/account"><i class="fa fa-home"></i><span class="hidden-xs hidden-sm"> <?php echo $this->translate("instagram/timeline"); ?></span></a>
            </li>
            <li<?php echo $this->route->params["controller"] == "Account" && $this->route->params["action"] == "Discover" ? ' class="active"' : ''; ?>>
                <a href="/account/discover"><i class="fa fa-search"></i><span class="hidden-xs hidden-sm"> <?php echo $this->translate("instagram/discover"); ?></span></a>
            </li>
            <li<?php echo $this->route->params["controller"] == "Account" && $this->route->params["action"] == "History" ? ' class="active"' : ''; ?>>
                <a href="/account/history"><i class="fa fa-heart"></i><span class="hidden-xs hidden-sm"> <?php echo $this->translate("instagram/history"); ?></span></a>
            </li>
            <li<?php echo ($this->route->params["controller"] == "Account" && ($this->route->params["action"] == "Settings" || $this->route->params["action"] == "Liked") || $this->route->params["controller"] == "User" && $this->route->params["usernameid"] == $logonPerson->member->instaID) ? ' class="active"' : ''; ?>>
                <a href="/user/<?php echo $logonPerson->member->instaID; ?>"><i class="fa fa-user"></i><span class="hidden-xs hidden-sm"> <?php echo $this->translate("instagram/myprofile"); ?></span></a>
            </li>
        </ul>
    </div>
</div>
<div class="bg-creditbar">
    <div class="container">
        <ul class="nav nav-justified nav-creditbar">
            <li>
                <a href="/tools/send-like"><i class="fa fa-heart text-danger"></i>
                    <span class="text"><?php echo $this->translate("instagram/send_like"); ?></span>
                    <span class="badge" id="begeniKrediCount"><?php echo $logonPerson->member["begeniKredi"]; ?></span></a>
            </li>
            <li>
                <a href="/tools/send-follower"><i class="fa fa-user-plus" style="color:#0068a7;"></i>
                    <span class="text"><?php echo $this->translate("instagram/send_follower"); ?></span>
                    <span class="badge" id="takipKrediCount"><?php echo $logonPerson->member["takipKredi"]; ?></span></a>
            </li>
            <li>
                <a href="/tools/send-comment"><i class="fa fa-comment text-warning"></i>
                    <span class="text"><?php echo $this->translate("instagram/send_comment"); ?></span>
                    <span class="badge" id="yorumKrediCount"><?php echo $logonPerson->member["yorumKredi"]; ?></span></a>
            </li>
            <li>
                <a href="/tools/story-view"><i class="fa fa-instagram text-warning"></i>
                    <span class="text"><?php echo $this->translate("instagram/story_view"); ?></span>
                    <span class="badge" id="storyKrediCount"><?php echo $logonPerson->member["storyKredi"]; ?></span></a>
            </li>
                               </ul>
                               
    </div>
     <div class="container">
        <ul class="nav nav-justified nav-creditbar">
                <li>
                    <a href="/tools/send-view-video"><i class="fa fa-video-camera text-warning"></i>
                        <span class="text"><?php echo $this->translate("instagram/video_view"); ?></span>
                        <span class="badge" id="viewKrediCount"><?php echo $logonPerson->member["videoKredi"]; ?></span></a>
                </li>
                 
                    <li>
                    <a href="/tools/send-save"><i class="fa fa-bookmark text-warning"></i>
                        <span class="text"><?php echo $this->translate("instagram/send_save"); ?></span>
                        <span class="badge" id="saveKrediCount"><?php echo $logonPerson->member["saveKredi"]; ?></span></a>
                </li>
                <li>
                    <a href="/live/send-view"><i class="fa fa-child text-warning"></i>
                        <span class="text"><?php echo $this->translate("Yanlı Yayın İzleyici"); ?></span>
                        <span class="badge" id="canliYayinKrediCount"><?php echo $logonPerson->member["canliYayinKredi"]; ?></span></a>
                </li>
            <li>                <a href="/tools/send-comment-like"><i class="fa fa-heart text-success"></i>                    <span class="text">Yorum Beğeni Gönder</span>                    <span class="badge" id="yorumKrediCount"><?php echo $logonPerson->member["yorumBegeniKredi"]; ?></span></a>            </li>
        </ul>
        </ul>
    </div>
</div>