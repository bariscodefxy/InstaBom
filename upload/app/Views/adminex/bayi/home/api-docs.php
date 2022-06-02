<?php
    /**
     * Created by PhpStorm.
     * User: Cihan
     * Date: 22.06.2018
     * Time: 10:36
     */
?>
    <div class="well api well-float">
        <div class="center-big-content-block">
            <h2 class="m-b-md">API Dökümanı</h2>
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <td class="width-40">HTTP Method</td>
                    <td>POST</td>
                </tr>
                <tr>
                    <td>API URL</td>
                    <td>http<?php echo Wow::get("project/onlyHttps") ? 's' : ''; ?>://<?php echo $_SERVER['SERVER_NAME'];
                            echo empty($baseUrl) ? '' : $baseUrl; ?>/api/v2
                    </td>
                </tr>
                <tr>
                    <td>Response format</td>
                    <td>JSON</td>
                </tr>
                </tbody>
            </table>
            <hr/>
            <h4 class="m-t-md"><strong>Servis Listesi</strong></h4>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="width-40">Parametreler</th>
                    <th>Açıklama</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>key</td>
                    <td>API Keyiniz</td>
                </tr>
                <tr>
                    <td>action</td>
                    <td>services</td>
                </tr>
                </tbody>
            </table>

            <p><strong>Örnek Geri Dönüş</strong></p>
            <pre>[
   {
        "service": "1",
        "name": "Instagram Takipçi",
        "type": "Default",
        "category": "Takipçi",
        "rate": "0.40",
        "min": "1",
        "max": 10000
    },
    {
        "service": "2",
        "name": "Instagram Beğeni",
        "type": "Default",
        "category": "Beğeni",
        "rate": "0.09",
        "min": "1",
        "max": 10000
    }
]
</pre>
            <hr/>
            <h4 class="m-t-md"><strong>Sipariş Ekleme</strong></h4>
            <p>
            </p>
            <form class="form-inline">
                <div class="form-group">
                    <select class="form-control input-sm" id="service_type">
                        <option value="0">Default</option>
                        <option value="2">Custom Comments</option>
                        <option value="100">Subscriptions</option>
                    </select>
                </div>
            </form>
            <p></p>
            <div id="type_0" style="display: none;">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="width-40">Parameters</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>key</td>
                        <td>API Keyiniz</td>
                    </tr>
                    <tr>
                        <td>action</td>
                        <td>add</td>
                    </tr>
                    <tr>
                        <td>service</td>
                        <td>Service ID</td>
                    </tr>
                    <tr>
                        <td>link</td>
                        <td>Sayfanın linki</td>
                    </tr>
                    <tr>
                        <td>quantity</td>
                        <td>Kaç adet?</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="type_2" style="display: none;">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="width-40">Parametreler</th>
                        <th>Açıklama</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>key</td>
                        <td>API Keyiniz</td>
                    </tr>
                    <tr>
                        <td>action</td>
                        <td>add</td>
                    </tr>
                    <tr>
                        <td>service</td>
                        <td>Servis ID</td>
                    </tr>
                    <tr>
                        <td>link</td>
                        <td>Sayfanın linki</td>
                    </tr>
                    <tr>
                        <td>comments</td>
                        <td>Yorumları \n ile ayırarak gönderiniz.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div id="type_100" style="display: block;">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="width-40">Parametreler</th>
                        <th>Açıklama</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>key</td>
                        <td>API Keyiniz</td>
                    </tr>
                    <tr>
                        <td>action</td>
                        <td>add</td>
                    </tr>
                    <tr>
                        <td>service</td>
                        <td>Servis ID</td>
                    </tr>
                    <tr>
                        <td>username</td>
                        <td>Kullanıcı Adı</td>
                    </tr>
                    <tr>
                        <td>min</td>
                        <td>Min. adet</td>
                    </tr>
                    <tr>
                        <td>max</td>
                        <td>Max. adet</td>
                    </tr>
                    <tr>
                        <td>runs</td>
                        <td>Bir defada kaç gönderim yapacak.</td>
                    </tr>
                    <tr>
                        <td>delay</td>
                        <td>Kaç dakikada bir çalışacak.</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <p><strong>Örnek Geri Dönüş</strong></p>
            <pre>{
    "order": 23501
}
</pre>
            <hr/>
            <h4 class="m-t-md"><strong>Sipariş Durumu</strong></h4>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="width-40">Parametreler</th>
                    <th>Açıklama</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>key</td>
                    <td>API Keyiniz</td>
                </tr>
                <tr>
                    <td>action</td>
                    <td>status</td>
                </tr>
                <tr>
                    <td>order</td>
                    <td>Sipariş ID</td>
                </tr>
                </tbody>
            </table>

            <p><strong>Örnek Geri Dönüş</strong></p>
            <pre>{
    "charge": "0.27819",
    "start_count": "3572",
    "status": "Partial",
    "remains": "157",
    "currency": "TRY"
}
</pre>
            <hr/>
            <h4 class="m-t-md"><strong>Çoklu sipariş durumu</strong></h4>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="width-40">Parametreler</th>
                    <th>Açıklama</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>key</td>
                    <td>API Keyiniz</td>
                </tr>
                <tr>
                    <td>action</td>
                    <td>status</td>
                </tr>
                <tr>
                    <td>orders</td>
                    <td>Sipariş id'lerini virgül ile ayırarak gönderiniz</td>
                </tr>
                </tbody>
            </table>

            <p><strong>Örnek Geri Dönüş</strong></p>
            <pre>{
    "1": {
        "charge": "0.27819",
        "start_count": "3572",
        "status": "Partial",
        "remains": "157",
        "currency": "TRY"
    },
    "100": {
        "charge": "1.44219",
        "start_count": "234",
        "status": "In progress",
        "remains": "10",
        "currency": "TRY"
    }
}
</pre>
            <hr/>
            <h4 class="m-t-md"><strong>Kullanıcı Bakiye</strong></h4>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="width-40">Parametreler</th>
                    <th>Açıklama</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>key</td>
                    <td>API Keyiniz</td>
                </tr>
                <tr>
                    <td>action</td>
                    <td>balance</td>
                </tr>
                </tbody>
            </table>

            <p><strong>Örnek Geri Dönüş</strong></p>
            <pre>{
    "balance": "100.84292",
    "currency": "TRY"
}
</pre>
        </div>
    </div>


<?php $this->section("section_scripts");
    $this->parent(); ?>
    <script>
        $("#service_type").length || $('div[id^="type_"]').show(), $("#service_type").change(function() {
            $("div[id^='type_']").hide();
            var e = $("#service_type").val();
            $("#type_" + e).show()
        }), $("#service_type").trigger("change");
    </script>
<?php $this->endSection(); ?>