<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */

    $servisler = $model["servisler"];
?>

    <div class="panel panel-default">
        <div class="panel-heading">
            InstaWeb SMM YENİ TALEP OLUŞTUR
        </div>
        <div class="panel-body">
            <?php if(isset($_COOKIE["okey"])) { ?>
                <div class="alert alert-success alert-block fade in">
                    <button type="button" class="close close-sm" data-dismiss="alert">
                        <i class="fa fa-times"></i>
                    </button>
                    <h4>
                        <i class="icon-ok-sign"></i>
                        <b>Başarılı!</b>
                    </h4>
                    <p>Talebiniz başarılı bir şekilde alınmıştır. Talebinizin durumunu
                        <a href="<?php echo Wow::get("project/adminPrefix"); ?>/api-smm/talepler" target="_blank"><b>talepler</b></a> bölümünden inceleyebilirsiniz.
                    </p>
                </div>
                <?php setcookie("okey", 1, time() - 3600);
            } ?>
            <h2 id="username-div"></h2>
            <h3>Bakiye : <label class="label label-success" id="bakiye"></label></h3>
            <hr/>
            <p>Bakiye yüklemek için skype : <b>ckn.89</b> <span style="font-size:11px;">(Min. bakiye ödemesi <u>100TL</u>)</span></p>
            <div class="col-md-12">
                <form id="talep-form" onsubmit="return false;">
                    <div class="panel">
                        <div class="panel-body extra-pad">
                            <h4 class="pros-title">Yeni Talep</h4>
                            <select class="form-control" id="kategoriler">
                                <option disabled selected>Kategori Seçiniz</option>
                            </select>
                            <br/>
                            <select class="form-control" id="servisler" name="servis">
                                <option disabled selected>Önce Kategori Seçiniz</option>
                            </select><br/>
                            <textarea id="desc-text" class="form-control" rows="5" disabled style="resize: none;">Servis Açıklaması</textarea>
                            <hr/>
                            <div class="input-elements" id="Default-type" style="display: none">
                                <div class="form-group">
                                    <label>Link</label>
                                    <input type="text" class="form-control" name="link" placeholder="İlgili linki buraya giriniz." autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Adet</label>
                                    <input type="number" class="form-control default-adet" onkeyup='return event.charCode >= 48 && event.charCode <= 57' name="adet" placeholder="Adet" autocomplete="off">
                                </div>
                            </div>
                            <div class="input-elements" id="CustomComments-type" style="display: none">
                                <div class="form-group">
                                    <label>Media Link</label>
                                    <input type="text" class="form-control" placeholder="İlgili linki buraya giriniz." name="medialink" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Adet</label>
                                    <input type="text" class="form-control custom-comments-count" onkeyup='return event.charCode >= 48 && event.charCode <= 57' placeholder="Adet" name="mediaadet" disabled autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Yorumlar (Her satıra 1 yorum)</label>
                                    <textarea class="form-control" name="yorumlar" id="customtext" placeholder="Her satıra 1 yorum gelecek" style="resize: vertical" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="input-elements" id="Subscriptions-type" style="display: none">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control" name="username" placeholder="Kullanıcı Adı Giriniz." autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Kaç Gönderiye Gidecek?</label>
                                    <input type="number" class="form-control toplamkacgonderi" name="kacgonderi" onkeyup='return event.charCode >= 48 && event.charCode <= 57' placeholder="Kaç gönderiye oto gönderilecek?" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Kaç Adet</label><br/>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control subs-min" onkeyup='$(".subs-max").val(parseInt(this.value)+1);return event.charCode >= 48 && event.charCode <= 57;' name="min" placeholder="Min" autocomplete="off">
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control subs-max" onkeyup='return event.charCode >= 48 && event.charCode <= 57;' name="max" placeholder="Max" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Bitiş Tarihi</label>
                                    <input class="form-control default-date-picker" size="16" type="text" value="" name="bitistarihi" placeholder="Bitiş Tarihi" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Toplam Tutar</label>
                                <input type="text" class="form-control toplamtutar" placeholder="0,00 TL" disabled>
                            </div>
                        </div>
                    </div>
                    <div style="text-align:center;">
                        <button type="submit" class="btn btn-success btn-lg submitbutton">Talep Gönder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->section("section_scripts");
    $this->parent(); ?>
    <script type="text/javascript">
        var data = JSON.parse('<?php echo str_replace("'", "\'", json_encode($servisler)); ?>');
        $('#username-div').html(data.user.username);
        $('#bakiye').html(data.user.bakiye + " ₺");
        $.each(data.servisler, function(a, b) {
            $('#kategoriler').append("<option value='" + a + "'>" + a + "</option>");
        });

        $('#kategoriler').on("change", function(e) {
            var kategori = $(this).val();
            $('#servisler').html('');
            $.each(data.servisler, function(a, b) {
                if(a === kategori) {
                    $.each(b, function(k, v) {
                        $('#servisler').append("<option data-min='" + v.data.min + "' data-max='" + v.data.max + "' data-type='" + v.data.type + "' data-price='" + v.data.rate + "' value='" + v.data.smmID + "'>" + v.data.name + " — " + v.data.rate + " TL</option>");
                    });
                }
            });
            textDescChange();
        });

        $('#servisler').on("change", function(e) {
            textDescChange();
        });

        var $price = 0;

        function textDescChange() {
            var min  = $("#servisler option:selected").attr("data-min");
            var max  = $("#servisler option:selected").attr("data-max");
            var type = $("#servisler option:selected").attr("data-type");
            $price   = $("#servisler option:selected").attr("data-price");

            type = type.replace(/\s/g, '');
            $('#desc-text').html('Minimum ' + min + ' adet gönderim yapılabilir.\nMaximum ' + max + ' adet gönderim yapılabilir.');

            $('.input-elements').hide();
            $('#' + type + '-type').show();
            $('.default-adet').val("");
            $('.custom-comments-count').val("");
            $('.subs-min').val("");
            $('.subs-max').val("");
            $('.toplamtutar').val("");
            $('.toplamkacgonderi').val("");
            $('#customtext').val("");
        }

        $('.default-date-picker').datepicker({
                                                 format: 'dd-mm-yyyy'
                                             });
        $('.default-date-picker').datepicker().on('changeDate', function(ev) {
            $('.datepicker').hide();
        });

        Array.prototype.clean = function(deleteValue) {
            for(var i = 0; i < this.length; i++) {
                if(this[i] == deleteValue) {
                    this.splice(i, 1);
                    i--;
                }
            }
            return this;
        };

        $('#customtext').on("keyup", function() {
            var cstm = $('#customtext').val().split('\n').filter(function(n) {
                return n.trim() != undefined && n.trim() != ''
            });
            $('.custom-comments-count').val(parseInt(cstm.length));
            tutarHesapla();
        });

        $('#customtext').on("change", function() {
            var cstm = $('#customtext').val().split('\n').filter(function(n) {
                return n.trim() != undefined && n.trim() != ''
            });
            $('.custom-comments-count').val(parseInt(cstm.length));
            tutarHesapla();
        });

        $('input').on("keyup", function() {
            tutarHesapla();
        });

        $('input').on("change", function() {
            tutarHesapla();
        });


        function tutarHesapla() {

            var defaultCount = parseInt($('.default-adet').val()) > 0 ? parseInt($('.default-adet').val()) : 0;
            var commentCount = parseInt($('.custom-comments-count').val()) > 0 ? parseInt($('.custom-comments-count').val()) : 0;
            var maxCount     = parseInt($('.subs-max').val()) > 0 ? parseInt($('.subs-max').val()) : 0;
            var kacGonderi   = parseInt($('.toplamkacgonderi').val()) > 0 ? parseInt($('.toplamkacgonderi').val()) : 0;
            var total        = parseInt(defaultCount + commentCount + maxCount);

            if(maxCount > 0) {
                total = (total * kacGonderi);
            }

            $('.toplamtutar').val((total * ($price / 1000)).toFixed(2) + " ₺");

        }


        $('.submitbutton').click(function() {
            $this = $('.submitbutton');
            $this.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Gönderiliyor..');
            $this.attr('disabled', 'disabled');
            var formData = $("#talep-form").serialize();
            $.ajax({
                       type       : "POST",
                       data       : formData,
                       success    : function(json) {
                           if(json.status === 1) {
                               window.location.href = "<?php echo Wow::get("project/adminPrefix"); ?>/api-smm/yeni-islem?okey=1";
                           } else {
                               alert(json.error);
                           }
                       }, complete: function() {
                    $this.text('Talep Gönder');
                    $this.removeAttr('disabled');
                }
                   })
        });

    </script>
<?php $this->endSection(); ?>