<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $users         = $model["users"];
    $nonFollowers  = $model["nonFollowers"];
    $nonFollowings = $model["nonFollowings"];
?>
<ul class="nav nav-tabs nav-justified nav-tabs-justified" style="margin-bottom: 15px;">
    <li class="active">
        <a href="#tabNonFollowers" data-toggle="tab">TAKİPÇİLERİN</a>
    </li>
    <li>
        <a href="#tabNonFollowings" data-toggle="tab" onclick="setTimeout(function(){$(window).scroll();}, 1000);">SEN</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade active in" id="tabNonFollowers">
        <div class="panel panel-default">
            <div class="panel-heading">
                Geri Takip Yapmayan Kullanıcılar
            </div>
            <div class="panel-body">
                <?php if(empty($nonFollowers)) { ?>
                    <p class="text-success">Woww. Tüm takipçilerin seni takip ediyor.</p>
                <? } else { ?>
                    <p class="text-danger">
                        <strong><span id="counterNonFollower"><?php echo count($nonFollowers); ?></span> kişi</strong> sadakatsiz çıktı. Malesef sen takip ediyorsun, ancak onlar seni takip etmiyor!
                    </p>
                    <hr/>
                    <div id="entry-list-container">
                        <div class="row" id="entry-list-row">
                            <?php foreach($nonFollowers as $username) {
                                $item = $users[$username]; ?>
                                <div class="entry-layout col-lg-3 col-md-3 col-sm-6 col-xs-12" id="follower<?php echo $item["pk"]; ?>">
                                    <div class="entry-thumb transition">
                                        <div class="entry-media">
                                            <div class="text-absolute">
                                                <a href="/user/<?php echo $item["pk"]; ?>"><strong><?php echo $item["username"]; ?></strong></a>
                                            </div>
                                            <div class="image">
                                                <a href="/user/<?php echo $item["pk"]; ?>"><img class="img-responsive lazy" data-original="<?php echo str_replace("http:","https:",str_replace("s150x150/", "", $item["profile_pic_url"])); ?>"/></a>
                                            </div>
                                        </div>
                                        <div class="action-links">
                                            <div class="btn-group btn-group-justified">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary" onclick="unfollowNonFollower('<?php echo $item["pk"]; ?>');">Takibi Bırak</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tabNonFollowings">
        <div class="panel panel-default">
            <div class="panel-heading">
                Geri Takip Etmediğiniz Kullanıcılar
            </div>
            <div class="panel-body">
                <?php if(empty($nonFollowings)) { ?>
                    <p class="text-success">Woww. Tüm takipçilerini takip ediyorsun.</p>
                <? } else { ?>
                    <p class="text-danger">
                        <strong><span id="counterNonFollowing"><?php echo count($nonFollowings); ?></span> kişi</strong>ye karşı sadakatsizsin. Malesef onlar seni takip ediyor, ancak sen onları takip etmiyorsun!
                    </p>
                    <hr/>
                    <div id="entry-list-container">
                        <div class="row" id="entry-list-row">
                            <?php foreach($nonFollowings as $username) {
                                $item = $users[$username]; ?>
                                <div class="entry-layout col-lg-3 col-md-3 col-sm-6 col-xs-12" id="following<?php echo $item["pk"]; ?>">
                                    <div class="entry-thumb transition">
                                        <div class="entry-media">
                                            <div class="text-absolute">
                                                <a href="/user/<?php echo $item["pk"]; ?>"><strong><?php echo $item["username"]; ?></strong></a>
                                            </div>
                                            <div class="image">
                                                <a href="/user/<?php echo $item["pk"]; ?>"><img class="img-responsive lazy" data-original="<?php echo str_replace("http:","https:",str_replace("s150x150/", "", $item["profile_pic_url"])); ?>"/></a>
                                            </div>
                                        </div>
                                        <div class="action-links">
                                            <div class="btn-group btn-group-justified">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary" onclick="followNonFollowing('<?php echo $item["pk"]; ?>');">Takip Et</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>



