<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
	 header ("Location:home");
?>
    <div class="cl10"></div>
    <div class="container">
        <div class="tab-content">
            <div class="tab-pane fade active in">
                <?php
                    $this->renderView("shared/reels", $this->get("reels"));
                ?>
                <hr />
                <?php
                    $this->renderView("shared/list-media", $model);
                ?>
            </div>
        </div>
    </div>


<?php $this->section("section_scripts");
    $this->parent(); ?>
    <script type="text/javascript" src="/assets/dash/dash.all.min.js"></script>
    <script type="text/javascript">
        function broadcastLiveVideo(videoUrl) {
            $('#modalLiveVideoBody').append('<video id="videoLiveVideo" style="width: 100%;height: 100%;" controls="true"></video>');
            var video,player;
            video = document.querySelector("#videoLiveVideo");
            player = dashjs.MediaPlayer().create();
            player.initialize(video, videoUrl, true);
        }

        $('#modalLiveVideo').on('hidden.bs.modal', function () {
            $('#modalLiveVideoBody').html('');
        });

        $reelLightBox = $('#reelsList');
        $reelLightBox.lightGallery({
                                       appendSubHtmlTo: '.lg-item',
                                       mode           : 'lg-fade',
                                       selector       : '.lightGalleryImage',
                                       hideBarsDelay  : 2000,
                                       enableSwipe    : false,
                                       enableDrag     : false,
                                       keyPress       : false
                                   });
        $reelLightBox.on('onAfterSlide.lg', function(event, prevIndex, index) {
            if(!$('.lg-outer .lg-item').eq(index).attr('data-fb')) {
                $('.lg-outer .lg-item').eq(index).attr('data-fb', 'loaded');
            }
        });
    </script>
<?php $this->endSection(); ?>


<?php $this->section("section_modals");
$this->parent(); ?>
<div class="modal fade" id="modalLiveVideo" style="z-index: 1051;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Canlı Yayın</h4>
            </div>
            <div class="modal-body" id="modalLiveVideoBody">
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
