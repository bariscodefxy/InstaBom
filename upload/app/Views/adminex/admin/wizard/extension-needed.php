<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "Upps. Eklenti Gerekiyor!");
?>
<div class="panel panel-default">
    <div class="panel-heading">
        EKSİK EKLENTİ
    </div>
    <div class="panel-body">
        <p class="text-danger">Bu aracı kullanabilmeniz için PHP 5.6 için Zip eklentisi kurulmuş olması gerekiyor! Görünüşe göre sunucunuzda bu eklenti kurulu değil. Sunucu yöneticinizden, Php 5.6 için Zip eklentisini kurmalarını isteyebilirsiniz.</p>
        <p>WHM kullanıcıları, zip eklentisini aktif etmek için şu adımları izleyebilir:</p>
        <ul>
            <li>Whm'ye giriş yapın.</li>
            <li>Sol kısımdaki arama kutusuna easyapache yazın. Sonrasında EasyApache3 veya EasyApache4 göreceksiniz. EasyApache4 varsa EasyApache4'ü kullanıyor olabilirsiniz. EasyApache'yi tıklayın.</li>
            <li>Açılan sayfada Currently Installed Packages yazısını ve hemen sağ tarafında Customize (Özelleştir) butonu yer alacak. Bu butona tıklayın. Sonrasında paketler listelenecek.</li>
            <li>Zip bir Php Extension paketi olduğu için sol kısımdaki tablardan PHP Extensions'u tıklayın.</li>
            <li>Arama kutusuna zip yazın. php56-php-zip listelenmiş olacak. 56 olması önemli, çünkü PHP sürümü 5.6 olması gerekiyor. php56-php-zip paketinin sağ tarafındaki onay işaretini tıklayarak install pozisyonuna getirin.</li>
            <li>Sonra sol kısımdaki tablardan Review tabına tıklayın. Aşağıya doğru inip Provision (Hazırlık) butonuna tıklayın. Sonrasında hazırlık tamamlanacak ve, aşağıda Complete (Tamamlandı) butonu görünecek. Bu butonu tıklayarak bitiriyoruz.</li>
        </ul>
    </div>
</div>