<?php 
            $domain=$_SERVER['SERVER_NAME'];
            $product="40";
            $licenseServer = "http://lisans.tiktokhile.com/api/";

            $postvalue="domain=$domain&product=".urlencode($product);

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $licenseServer);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvalue);
            $result= json_decode(curl_exec($ch), true);
            curl_close($ch);

            if($result['status'] != 200) {
            $html = "<div align='center'>
    <table width='100%' border='0' style='padding:15px; border-color:#F00; border-style:solid; background-color:#FF6C70; font-family:Tahoma, Geneva, sans-serif; font-size:22px; color:white;'>

    <tr>

        <td><b><center>Lisanssiz Kullanim </center><center> Yazilimciniz ile </center><center>iletisime Gecin </center><center> +90 538 700 84 19</center></b></td >

    </tr>

    </table>

</div>";
            $search = '<%returnmessage%>';
            $replace = $result['message'];
            $html = str_replace($search, $replace, $html);


            die( $html );

            }
            ?>