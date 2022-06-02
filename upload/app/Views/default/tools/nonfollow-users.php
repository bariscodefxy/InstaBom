<?php
    /**
     * @var \Wow\Template\View $this
     */
?>
<div class="container">
    <div class="cl10"></div>
    <div class="row">
        <div class="col-sm-8 col-md-9">
            <h4 style="margin-top: 0;">Geri Takip Yapmayan / Takip Edilmeyen Kullanıcılar Aracı</h4>
            <p>Geri Takip Yapmayan / Takip Edilmeyen Kullanıcılar aracı ile, sizin takip ettiğiniz, ancak sizi takip etmeyen kullanıcıları tespit edebilirsiniz. Duruma göre siz de takibi bırakabilirsiniz. Aynı şekilde sizi takip eden, ancak sizin takip etmediğiniz kullanıcıları da tespit edebilirsiniz.</p>
            <p>Bu araç şu an için ücretsizdir. Ancak ilerleyen süreçte ücretli hale getirilebilir. Şimdilik, ücretsiz olarak kullanmanın keyfini çıkarın.</p>
            <div id="containerForUsers">
                <p>
                    <i class="fa fa-spinner fa-spin fa-fw fa-3x"></i> Yükleniyor.. Takip Ettikleriniz / Takipçileriniz çok fazla ise bu işlem biraz zaman alabilir. Sabırlı olun...
                </p>
            </div>
        </div>
        <div class="col-sm-4 col-md-3">
            <?php $this->renderView("tools/sidebar"); ?>
        </div>
    </div>
</div>
<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({url: '/tools/nonfollow-users?formType=findUsers', type: 'POST'}).done(function(data) {
            $('#containerForUsers').html(data);
            $(".lazy").show().lazyload({threshold: 500}).removeClass("lazy");
        });
    });

    function unfollowNonFollower(userid) {
        div = $('#follower' + userid);
        div.hide();
        counter = $('#counterNonFollower');
        $.ajax({url: '/account/unfollow', dataType: 'json', type: 'POST', data: 'id=' + userid}).done(function(data) {
            if(data.status == 'error') {
                div.show();
            }
            else {
                counter.html(parseInt(counter.html()) - 1);
            }
        });
    }

    function followNonFollowing(userid) {
        div = $('#following' + userid);
        div.hide();
        counter = $('#counterNonFollowing');
        $.ajax({url: '/account/follow', dataType: 'json', type: 'POST', data: 'id=' + userid}).done(function(data) {
            if(data.status == 'error') {
                div.show();
            }
            else {
                counter.html(parseInt(counter.html()) - 1);
            }
        });
    }
</script>
<?php $this->endSection(); ?>
