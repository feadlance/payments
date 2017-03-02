# Payments

İçerisinde bulunan ödeme yöntemlerinin PHP'ye kolay entegre edilebilmesi için hazırlandı.

---

### Kurulum

Composer ile:

    $ composer require weblebby/payments 1.0

Manuel:

    Dosyaları indirin, src klasörünün içindeki dosyaları alıp sayfanıza dahil edin.
    
---

### Kullanım

```php
<?php

use Weblebby\Payments\BatigamePayment;
use Weblebby\Payments\PaymentException;

// Composer ile:
require __DIR__ . '/vendor/autoload.php';

// Manuel:
# Dosyaları sayfaya dahil edin.

$config = [
  'id' => '{BATIHOST_USER_ID}',
  'secret' => '{BATIHOST_GUVENLIK_KODU}'
];

$payment = new BatigamePayment($config);
```

HTML İçin Form Oluşturmak

```php
$payment->htmlForm->success_url = '{ODEME_OLDU_URL}';
$payment->htmlForm->error_url = '{ODEME_OLMADI_URL}';
$payment->htmlForm->vip_name = '{VIP_NAME}';
$payment->htmlForm->report_email = '{REPORT_EMAIL}';
$payment->htmlForm->only_email = '{ONLY_EMAIL}';
$payment->htmlForm->post_url = '{POST_URL}';
```

```html
<!DOCTYPE html>
<html>
  <body>
    <?php echo $payment->openHtmlForm(); ?>
    
    <input type="text" name="oyuncu" placeholder="kullanıcı adı..."><br>
    <input type="text" name="amount" placeholder="kredi miktarı"><br>
    <input type="checkbox" name="odemeturu" value="kredikarti"> Kredi Kartı<br>
    <input type="submit" value="Gönder">
    
    <?php echo $payment->closeHtmlForm(); ?>
  </body>
</html>
```

Geri Dönen Post İşlemi

```php
  // {posturl} sayfası.
  
  try {
    /**
     * Gelen post bilgilerini $post değişkenine atadık. [trans_id, username, credit, secret]
     * handle() fonksiyonu, gelen postun eksik olup olmadığını ve güvenlik kodunun doğruluğunu kontrol edip geri dönüş yapıyor.
     * Eğer kontrolde bir sorun varsa catch() içine atıyor.
     */
    $post = $payment->handle();
    
    // Gelen $post değişkeninden aynı trans_id ile oyuncuya kredi yüklenmiş mi kontrol edebilir, yüklenmemiş ise kredisini yükleyebilirsiniz.
  } catch (PaymentException $e) {
    // Güvenlik kodu yanlış ya da gelen post eksik.
    echo $e->getMessage();
  }
```
