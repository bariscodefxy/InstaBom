<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $model
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $uyelik      = $logonPerson->member;
    $userInfo    = $model["user"];
    $helper      = new \App\Libraries\Helpers();
    $isMobile    = $helper->is_mobile();
?>
<div class="jumbotron" style="margin-bottom: 0;padding-top: 5px;padding-bottom: 5px;">
    <div class="container">
        <?php if(!$isMobile) { ?>
            <div class="row">
                <div class="col-sm-3 col-md-2" style="padding-top:15px;">
                    <a rel="fancyboxImage" href="<?php echo isset($userInfo["hd_profile_pic_versions"]) ? end($userInfo["hd_profile_pic_versions"])["url"] : str_replace("http:","https:",$userInfo["profile_pic_url"]); ?>"><img class="img-responsive img-circle" id="profilePhoto" src="<?php echo str_replace("http:","https:",$userInfo["profile_pic_url"]); ?>"/></a>
                    <?php if($uyelik["instaID"] == $userInfo["pk"]) { ?>
                        <div class="btn-group btn-group-xs btn-group-justified" style="margin-top: 10px;">
                            <a class="btn btn-default" href="#modalChangeProfilePhoto" data-toggle="modal" onclick="changeProfilePhoto();">Değiştir</a>
                            <a class="btn btn-danger" href="javascript:void(0);" onclick="removeProfilePhoto();">Kaldır</a>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-sm-9 col-md-10">
                    <h1><?php echo $userInfo["full_name"]; ?>
                        <small>@<?php echo $userInfo["username"]; ?></small>
                    </h1>
                    <?php if($uyelik["instaID"] != $userInfo["pk"]) { ?>
                        <p>
                        <div class="btn-group">
                            <?php $userFriendship = $this->get("userFriendship");
                                if($userFriendship["blocking"] == 1) { ?>
                                    <button id="btnUserFollow" class="btn btn-default" onclick="unblock('<?php echo $userInfo["pk"]; ?>');">
                                        <i class="fa fa-unlock"></i> Engellemeyi Kaldır
                                    </button>
                                <?php } elseif($userFriendship["following"] == 1) { ?>
                                    <button id="btnUserFollow" class="btn btn-success" onclick="unfollow('<?php echo $userInfo["pk"]; ?>');">
                                        <i class="fa fa-check"></i> Takip Ediyorsun
                                    </button>
                                <?php } elseif($userFriendship["outgoing_request"] == 1) { ?>
                                    <button id="btnUserFollow" class="btn btn-default" onclick="unfollow('<?php echo $userInfo["pk"]; ?>');">
                                        <i class="fa fa-clock-o"></i> İstek Gönderildi
                                    </button>
                                <?php } elseif($userFriendship["is_private"] == 1) { ?>
                                    <button id="btnUserFollow" class="btn btn-primary" onclick="follow('<?php echo $userInfo["pk"]; ?>');">
                                        <i class="fa fa-plus"></i> Takip Et (İstek Gönder)
                                    </button>
                                <?php } else { ?>
                                    <button id="btnUserFollow" class="btn btn-primary" onclick="follow('<?php echo $userInfo["pk"]; ?>');">
                                        <i class="fa fa-plus"></i> Takip Et
                                    </button>
                                <?php } ?>
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li id="btnPopUnblock" style="display:<?php echo $userFriendship["blocking"] == 1 ? '' : 'none'; ?>;">
                                    <a href="javascript:void(0);" onclick="unblock('<?php echo $userInfo["pk"]; ?>')">Engellemeyi Kaldır</a>
                                </li>
                                <li id="btnPopBlock" style="display:<?php echo $userFriendship["blocking"] != 1 ? '' : 'none'; ?>;">
                                    <a href="javascript:void(0);" onclick="block('<?php echo $userInfo["pk"]; ?>')">Engelle</a>
                                </li>
                            </ul>
                        </div>
                        </p>
                    <?php } ?>
                    <p><?php echo $userInfo["biography"]; ?></p>
                    <?php if(!empty($userInfo["external_url"])) { ?><p style="font-size: 13px;">
                        <a href="<?php echo str_replace("http:","https:",$userInfo["external_url"]); ?>" target="_blank"><?php echo str_replace("http:","https:",strip_tags($userInfo["external_url"])); ?></a>
                        </p><?php } ?>
                    <?php if($uyelik["instaID"] != $userInfo["pk"]) { ?>
                        <p>
                            <a class="btn btn-xs btn-warning" href="javascript:void(0);" onclick="newMessage('<?php echo $userInfo["pk"]; ?>')">Kullanıcıya DM Gönder</a>
                        </p>
                    <?php } ?>
                    <?php if(!empty($userInfo["profile_context"])) { ?>
                        <p class="text-primary" style="font-size:12px;"><?php echo $userInfo["profile_context"]; ?></p><?php } ?>
                    <div class="btn-group btn-group-justified" style="margin-top:5px;">
                        <a class="btn btn-default" href="/user/<?php echo $userInfo["pk"]; ?>"><?php echo $userInfo["media_count"]; ?> Gönderi</a>
                        <a class="btn btn-default" href="/user/<?php echo $userInfo["pk"]; ?>/follower"><?php echo $userInfo["follower_count"]; ?> Takipçi</a>
                        <a class="btn btn-default" href="/user/<?php echo $userInfo["pk"]; ?>/following"><?php echo $userInfo["following_count"]; ?> Takip</a>
                    </div>
                </div>
            </div>
        <?php }
            if($isMobile) { ?>
                <div class="row">
                    <div class="col-xs-4">
                        <a rel="fancyboxImage" href="<?php echo isset($userInfo["hd_profile_pic_versions"]) ? end($userInfo["hd_profile_pic_versions"])["url"] : str_replace("http:","https:",$userInfo["profile_pic_url"]); ?>"><img class="img-responsive img-circle" id="profilePhoto" src="<?php echo str_replace("http:","https:",$userInfo["profile_pic_url"]); ?>"/></a>
                        <?php if($uyelik["instaID"] == $userInfo["pk"]) { ?>
                            <div class="btn-group btn-group-xs btn-group-justified" style="margin-top: 10px;">
                                <a class="btn btn-default" href="#modalChangeProfilePhoto" data-toggle="modal" onclick="changeProfilePhoto();">Değiştir</a>
                                <a class="btn btn-danger" href="javascript:void(0);" onclick="removeProfilePhoto();">Kaldır</a>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-xs-8">
                        <h4><?php echo $userInfo["full_name"]; ?>
                            <br/>
                            <small>@<?php echo $userInfo["username"]; ?></small>
                        </h4>
                        <?php if($uyelik["instaID"] != $userInfo["pk"]) { ?>
                            <p>
                            <div class="btn-group">
                                <?php $userFriendship = $this->get("userFriendship");
                                    if($userFriendship["blocking"] == 1) { ?>
                                        <button id="btnUserFollow" class="btn btn-default" onclick="unblock('<?php echo $userInfo["pk"]; ?>');">
                                            <i class="fa fa-unlock"></i> Engellemeyi Kaldır
                                        </button>
                                    <?php } elseif($userFriendship["following"] == 1) { ?>
                                        <button id="btnUserFollow" class="btn btn-success" onclick="unfollow('<?php echo $userInfo["pk"]; ?>');">
                                            <i class="fa fa-check"></i> Takip Ediyorsun
                                        </button>
                                    <?php } elseif($userFriendship["outgoing_request"] == 1) { ?>
                                        <button id="btnUserFollow" class="btn btn-default" onclick="unfollow('<?php echo $userInfo["pk"]; ?>');">
                                            <i class="fa fa-clock-o"></i> İstek Gönderildi
                                        </button>
                                    <?php } elseif($userFriendship["is_private"] == 1) { ?>
                                        <button id="btnUserFollow" class="btn btn-primary" onclick="follow('<?php echo $userInfo["pk"]; ?>');">
                                            <i class="fa fa-plus"></i> Takip Et (İstek Gönder)
                                        </button>
                                    <?php } else { ?>
                                        <button id="btnUserFollow" class="btn btn-primary" onclick="follow('<?php echo $userInfo["pk"]; ?>');">
                                            <i class="fa fa-plus"></i> Takip Et
                                        </button>
                                    <?php } ?>
                                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span></button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li id="btnPopUnblock" style="display:<?php echo $userFriendship["blocking"] == 1 ? '' : 'none'; ?>;">
                                        <a href="javascript:void(0);" onclick="unblock('<?php echo $userInfo["pk"]; ?>')">Engellemeyi Kaldır</a>
                                    </li>
                                    <li id="btnPopBlock" style="display:<?php echo $userFriendship["blocking"] != 1 ? '' : 'none'; ?>;">
                                        <a href="javascript:void(0);" onclick="block('<?php echo $userInfo["pk"]; ?>')">Engelle</a>
                                    </li>
                                </ul>
                            </div>
                            </p>
                        <?php } ?>
                        <p style="font-size: 13px;"><?php echo $userInfo["biography"]; ?></p>
                        <?php if(!empty($userInfo["external_url"])) { ?><p style="font-size: 13px;">
                            <a href="<?php echo str_replace("http:","https:",$userInfo["external_url"]); ?>" target="_blank"><?php echo str_replace("http:","https:",strip_tags($userInfo["external_url"])); ?></a>
                            </p><?php } ?>
                        <?php if(!empty($userInfo["profile_context"])) { ?>
                            <p class="text-primary" style="font-size:12px;"><?php echo $userInfo["profile_context"]; ?></p><?php } ?>
                    </div>
                </div>
                <div class="btn-group btn-group-justified" style="margin-top:5px;">
                    <a class="btn btn-default" href="/user/<?php echo $userInfo["pk"]; ?>"><?php echo $userInfo["media_count"]; ?> Gönderi</a>
                    <a class="btn btn-default" href="/user/<?php echo $userInfo["pk"]; ?>/follower"><?php echo $userInfo["follower_count"]; ?> Takipçi</a>
                    <a class="btn btn-default" href="/user/<?php echo $userInfo["pk"]; ?>/following"><?php echo $userInfo["following_count"]; ?> Takip</a>
                </div>
            <?php } ?>
    </div>
</div>