<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */

    @$talepler = $model["talepler"];
?>

    <div class="panel panel-default">
        <div class="panel-heading">
            InstaWeb SMM TALEPLER
        </div>
        <div class="panel-body">
            <div class="hata-text text text-warning"></div>
            <div id="loader" class="text-center"><img src="/assets/images/loader.svg"/><br/>
                <p>Talepler Yükleniyor..</p></div>

            <table id="talep-table" class="table table-hover general-table" style="display: none;">
                <thead>
                <tr>
                    <th>TalepID</th>
                    <th>Tarih</th>
                    <th>Servis</th>
                    <th>Link</th>
                    <th>Detay</th>
                    <th>Tip</th>
                    <th>Durum</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div style="margin: 0 auto;" class="pager">
                <div id="pager-talep" class="btn-group">

                </div>
            </div>
        </div>
        <p>
            <b>Not</b>: İptal edilen yada yarım kalan işlemlerin bakiyeleri 12 saat içerisinde hesabınıza geri aktarılmaktadır.
        </p>
    </div>

<?php $this->section("section_scripts");
    $this->parent(); ?>
    <script type="text/javascript">
        talepPage(<?php echo $model["sayfa"]; ?>);

        function talepPage(page) {
            $.ajax({
                       type   : "POST",
                       url    : "<?php echo Wow::get("project/adminPrefix"); ?>/api-smm/talepler/" + page,
                       success: function(json) {
                           if(typeof json[0] !== 'undefined' && json[0].status === 1) {
                               var html = "";
                               $.each(json[0].talepler, function(k, v) {
                                   html += '<tr>\n' +
                                           '<td>' + v.info.talepID + '</td>\n' +
                                           '<td>' + v.info.tarih + '</td>\n' +
                                           '<td>' + v.info.service + '</td>\n';
                                   if(v.info.serviceType == 'Subscriptions') {
                                       html += '<td>' + v.smm.username + '</td>\n' +
                                               '<td>Gönderilen: <b>' + v.smm.gonderilen + '</b>/<b>' + v.smm.kacgonderi + '</b> / Min : <b>' + v.smm.min + '</b> / Max : <b>' + v.smm.max + '</b><br/>Bitiş Tarihi : <b>' + v.smm.bitistarihi + '</b></td>\n';
                                   } else {
                                       html += '<td><a href="' + v.smm.link + '" target="_blank">' + v.smm.link + '</a></td>\n' +
                                               '<td>Talep: <b>' + v.smm.adet + '</b> / Başlangıç : <b>' + v.smm.baslangic + '</b> / Kalan : <b>' + v.smm.kalan + '</b></td>\n';
                                   }

                                   var type      = "Normal";
                                   var typeClass = "default";
                                   if(v.info.serviceType == "Custom Comments") {
                                       type      = "Manuel Yorum";
                                       typeClass = "warning";
                                   } else if(v.info.serviceType == "Subscriptions") {
                                       type      = "Oto Gönderim";
                                       typeClass = "primary";
                                   }

                                   html += '<td class="text-center"><span class="label label-' + typeClass + ' label-mini">' + type + '</span></td>\n';

                                   var durum      = "";
                                   var durumClass = "";
                                   if(v.smm.durum == "Completed") {
                                       durum      = "Tamamlandı";
                                       durumClass = "success";
                                   } else if(v.smm.durum == "Processing" || v.smm.durum == "In progress") {
                                       durum      = "Gönderiliyor";
                                       durumClass = "primary";
                                   } else if(v.smm.durum == "Partial") {
                                       durum      = "Yarım Kaldı";
                                       durumClass = "warning";
                                   } else if(v.smm.durum == "Canceled") {
                                       durum      = "İptal Edildi";
                                       durumClass = "danger";
                                   } else if(v.smm.durum == "Active") {
                                       durum      = "Aktif";
                                       durumClass = "info";
                                   } else if(v.smm.durum == "Waiting") {
                                       durum      = "Bekliyor";
                                       durumClass = "default";
                                   } else {
                                       durum      = v.smm.durum;
                                       durumClass = "default";
                                   }
                                   html += '<td class="text-center"><span class="label label-' + durumClass + ' label-mini">' + durum + '</span></td>\n' +
                                           '</tr>';

                               });
                               $('tbody').append(html);

                               var totalPage = json.page.total / 100;
                               var nowPage   = json.page.now;
                               if(totalPage > 1) {
                                   var htmlx = "";
                                   for(var i = 1; i <= totalPage; i++) {
                                       htmlx += '<button class="pagechange btn btn-' + (i == nowPage ? 'success' : 'default') + '" data-page="' + i + '" type="button">' + i + '</button>';
                                   }
                                   $('#pager-talep').html(htmlx);
                               }
                               $('.pagechange').click(function() {
                                   var page = $(this).attr("data-page");
                                   talepPage(page);
                               });

                               $('#talep-table').show();
                           } else if(json.status == 2) {
                               $('.hata-text').html(json.error);
                           } else {
                               alert(json.error);
                           }
                           $('#loader').hide();
                       }
                   });
        }
    </script>
<?php $this->endSection(); ?>