<?php

namespace App\Support;

use App\Models\NotificationTemplate;

/**
 * Müşteriye giden SMS / e-posta metinleri.
 *
 * Varsayılan metinler burada; admin panelden (Sistem > Bildirim Şablonları) değiştirilen
 * metin notification_templates tablosunda tutulur ve varsayılanın yerine geçer.
 * Metinlerde {{DEĞİŞKEN}} biçimindeki yer tutucular gönderim anında doldurulur.
 * E-posta gövdesi HTML'dir; ortak üst/alt bilgiyle (layout) sarılarak gönderilir.
 * Adı _HTML ile biten değişkenler (ör. ORDER_ITEMS_HTML) kodda üretilen hazır HTML bloklarıdır.
 */
class NotificationTemplates
{
    public const DEFINITIONS = [
        'favorite_price_changed' => [
            'name' => 'Favori eserin fiyatı değişti',
            'description' => 'Admin bir eserin TL fiyatını değiştirdiğinde, eseri favorisine eklemiş üyelere gider (satılmış eserlerde gitmez).',
            'variables' => [
                'FIRSTNAME' => 'Üyenin adı', 'LASTNAME' => 'Üyenin soyadı', 'ARTWORK_TITLE' => 'Eser adı', 'ARTIST_NAME' => 'Sanatçı',
                'OLD_PRICE' => 'Eski fiyat (TL, ör. 65.000)', 'NEW_PRICE' => 'Yeni fiyat (TL)', 'ARTWORK_URL' => 'Eser sayfası adresi',
            ],
            'sms' => "Favorilerinizdeki bir eserin fiyatı {{OLD_PRICE}} TL'den {{NEW_PRICE}} TL'ye güncellendi. Detayları görmek için: beartshare.com",
            'email_subject' => 'Favori eserinizin fiyatı güncellendi',
            'email_body' => "<p class=\"label\">Favori Bildirimi</p>\n<h2>Sayın {{FIRSTNAME}} {{LASTNAME}},</h2>\n<p>Favorilerinizde bulunan bir eserin fiyatı güncellenmiştir.</p>\n<div class=\"box\"><strong>{{ARTWORK_TITLE}}</strong><br><span class=\"muted\">{{ARTIST_NAME}}</span><br><br><s class=\"muted\">{{OLD_PRICE}} TL</s> &nbsp;<span class=\"price\">{{NEW_PRICE}} TL</span></div>\n<p>Eseri incelemek ve satın almak için aşağıdaki butonu kullanabilirsiniz.</p>\n<p><a href=\"{{ARTWORK_URL}}\" class=\"button\">Eseri Görüntüle</a></p>",
        ],
        'favorite_reserved' => [
            'name' => 'Favori eser rezerve edildi',
            'description' => 'Bir üye eseri sepetten satın almaya başladığında (havale siparişi) eseri favorisine eklemiş diğer üyelere gider. Aynı üyeye aynı eser için bir kez gider.',
            'variables' => [
                'FIRSTNAME' => 'Üyenin adı', 'LASTNAME' => 'Üyenin soyadı', 'ARTWORK_TITLE' => 'Eser adı', 'ARTIST_NAME' => 'Sanatçı', 'ARTWORK_URL' => 'Eser sayfası adresi',
            ],
            'sms' => 'Favorilerinizdeki “{{ARTWORK_TITLE}}“ başlıklı eser başka bir koleksiyoner tarafından rezerve edildi. Eseri incelemek için: beartshare.com',
            'email_subject' => 'Favori eseriniz rezerve edildi',
            'email_body' => "<p class=\"label\">Favori Bildirimi</p>\n<h2>Sayın {{FIRSTNAME}} {{LASTNAME}},</h2>\n<p>Favorilerinizde bulunan bir eser başka bir müşteri tarafından rezerve edilmiştir.</p>\n<div class=\"box\"><strong>{{ARTWORK_TITLE}}</strong><br><span class=\"muted\">{{ARTIST_NAME}}</span></div>\n<p>Satın alma tamamlanmazsa eser yeniden satışa açılır.</p>\n<p><a href=\"{{ARTWORK_URL}}\" class=\"button\">Eseri Görüntüle</a></p>",
        ],
        'favorite_sold' => [
            'name' => 'Favori eser satıldı',
            'description' => 'Eser satıldığında (sipariş onaylanınca ya da admin "Satıldı" işaretleyince) eseri favorisine eklemiş diğer üyelere gider. Aynı üyeye aynı eser için bir kez gider.',
            'variables' => [
                'FIRSTNAME' => 'Üyenin adı', 'LASTNAME' => 'Üyenin soyadı', 'ARTWORK_TITLE' => 'Eser adı', 'ARTIST_NAME' => 'Sanatçı', 'ARTWORKS_URL' => 'Eserler sayfası adresi',
            ],
            'sms' => 'Favorilerinizdeki “{{ARTWORK_TITLE}}“ başlıklı eser başka bir koleksiyoner tarafından satın alındı. Yeni eserleri keşfetmek için: beartshare.com',
            'email_subject' => 'Favori eseriniz satıldı',
            'email_body' => "<p class=\"label\">Favori Bildirimi</p>\n<h2>Sayın {{FIRSTNAME}} {{LASTNAME}},</h2>\n<p>Favorilerinizde bulunan bir eser başka bir kullanıcı tarafından satın alınmıştır.</p>\n<div class=\"box\"><strong>{{ARTWORK_TITLE}}</strong><br><span class=\"muted\">{{ARTIST_NAME}}</span></div>\n<p>Sitemizde satışta olan tüm eserlere göz atmak için aşağıdaki butona tıklayabilirsiniz.</p>\n<p><a href=\"{{ARTWORKS_URL}}\" class=\"button\">BeArtShare.com'u Ziyaret Et</a></p>",
        ],
        'order_confirmed' => [
            'name' => 'Sipariş onaylandı (alıcı)',
            'description' => 'Admin siparişi onayladığında alıcıya gider; alıcının kazandığı ArtPuan bildirilir.',
            'variables' => [
                'FIRSTNAME' => 'Alıcının adı', 'LASTNAME' => 'Alıcının soyadı', 'ORDER_NUMBER' => 'Sipariş numarası', 'ORDER_ID' => 'Sipariş ID',
                'ARTPUAN' => 'Kazanılan ArtPuan', 'ARTPUAN_BALANCE' => 'Toplam ArtPuan bakiyesi',
            ],
            'sms' => "Sayın {{FIRSTNAME}} {{LASTNAME}},\n\n{{ORDER_NUMBER}} numaralı eser alımınız onaylandı. Bu alımınızla {{ARTPUAN}} ArtPuan® kazandınız. Bir sonraki eser alımınızda ArtPuan®’larınızı kullanabilirsiniz.\n\nBizi tercih ettiğiniz için teşekkür ederiz!\n\nbeartshare.com",
            'email_subject' => 'Eser alımınız onaylandı',
            'email_body' => "<p class=\"label\">Sipariş Onayı</p>\n<h2>Sayın {{FIRSTNAME}} {{LASTNAME}},</h2>\n<p>Siparişiniz başarıyla onaylanmıştır. BeArtShare'i tercih ettiğiniz için teşekkür ederiz.</p>\n<div class=\"box\">Sipariş Numarası: <strong>{{ORDER_NUMBER}}</strong></div>\n<p>Bu alımınızla <strong>{{ARTPUAN}} ArtPuan®</strong> kazandınız; toplam ArtPuan® bakiyeniz <strong>{{ARTPUAN_BALANCE}}</strong>. ArtPuan®’larınızı bir sonraki eser alımınızda kullanabilirsiniz.</p>\n<p>Siparişiniz en hızlı şekilde işleme alınacaktır. Sipariş durumu ve teslimat ile ilgili güncellemeler için e-postanızı takip etmeye devam edin.</p>\n<p>BeArtShare ile eserleri avantajlı fiyatlarla koleksiyonunuza dahil etmek için bizi takip edin!</p>",
        ],
        'referrer_artpuan' => [
            'name' => 'Referanstan ArtPuan kazanıldı',
            'description' => 'Davet edilen üyenin siparişi onaylanınca davet eden üyeye gider.',
            'variables' => [
                'FIRSTNAME' => 'Davet edenin adı', 'LASTNAME' => 'Davet edenin soyadı', 'BUYER_FIRSTNAME' => 'Alım yapan üyenin adı',
                'ARTPUAN' => 'Kazanılan ArtPuan', 'ARTPUAN_BALANCE' => 'Toplam ArtPuan bakiyesi', 'ORDER_NUMBER' => 'Sipariş numarası',
            ],
            'sms' => 'BeArtShare: Referansınız {{BUYER_FIRSTNAME}} bir eser alımı yaptı. {{ARTPUAN}} ArtPuan® kazandınız!',
            'email_subject' => 'Referansınızdan ArtPuan® kazandınız',
            'email_body' => "<p class=\"label\">ArtPuan®</p>\n<h2>Sayın {{FIRSTNAME}} {{LASTNAME}},</h2>\n<p>Referansınız <strong>{{BUYER_FIRSTNAME}}</strong> bir eser alımı gerçekleştirdi ve siz ArtPuan® kazandınız.</p>\n<div class=\"box\">Kazanılan: <strong>{{ARTPUAN}} ArtPuan®</strong><br>Toplam bakiyeniz: <strong>{{ARTPUAN_BALANCE}} ArtPuan®</strong></div>\n<p>ArtPuan®’larınızı sonraki eser alımlarınızda kullanabilirsiniz.</p>",
        ],
        'manual_artpuan' => [
            'name' => 'Hesaba ArtPuan eklendi (manuel)',
            'description' => 'Admin bir üyeye elle ArtPuan eklediğinde gider.',
            'variables' => [
                'FIRSTNAME' => 'Üyenin adı', 'LASTNAME' => 'Üyenin soyadı', 'ARTPUAN' => 'Eklenen ArtPuan', 'DESCRIPTION' => 'Açıklama',
                'ARTPUAN_BALANCE' => 'Toplam ArtPuan bakiyesi',
            ],
            'sms' => 'BeArtShare: Hesabınıza {{ARTPUAN}} ArtPuan® eklendi. Açıklama: {{DESCRIPTION}}. Toplam bakiyeniz: {{ARTPUAN_BALANCE}}',
            'email_subject' => 'Hesabınıza ArtPuan® eklendi',
            'email_body' => "<p class=\"label\">ArtPuan®</p>\n<h2>Sayın {{FIRSTNAME}} {{LASTNAME}},</h2>\n<p>Hesabınıza ArtPuan® eklendi.</p>\n<div class=\"box\">Eklenen: <strong>{{ARTPUAN}} ArtPuan®</strong><br>Açıklama: {{DESCRIPTION}}<br>Toplam bakiyeniz: <strong>{{ARTPUAN_BALANCE}} ArtPuan®</strong></div>\n<p>ArtPuan®’larınızı sonraki eser alımlarınızda kullanabilirsiniz.</p>",
        ],
        'order_created' => [
            'name' => 'Sipariş alındı (alıcı)',
            'description' => 'Sipariş oluşunca (havalede hemen, kartta ödeme başarılı olunca) alıcıya gider. Havale bilgileri yalnızca havale siparişlerinde dolar.',
            'variables' => [
                'FIRSTNAME' => 'Alıcının adı', 'LASTNAME' => 'Alıcının soyadı', 'ORDER_NUMBER' => 'Sipariş numarası', 'ORDER_ID' => 'Sipariş ID',
                'TOTAL' => 'Toplam tutar (TL)', 'PAYMENT_CODE' => 'Havale açıklama kodu', 'BANK_URL' => 'Banka hesapları sayfası',
                'BANK_TRANSFER_SMS' => 'Havalede SMS ek bilgisi (kartta boş)',
                'PAYMENT_INFO_HTML' => 'Havale kodu, banka hesapları ve uyarılar (kartta boş)', 'ORDER_ITEMS_HTML' => 'Eser listesi ve toplam tablosu',
            ],
            'sms' => 'BeArtShare: Siparişiniz başarıyla oluşturuldu. Sipariş No: {{ORDER_NUMBER}}. {{BANK_TRANSFER_SMS}}',
            'email_subject' => 'Siparişiniz alındı - {{ORDER_NUMBER}}',
            'email_body' => "<p class=\"label\">Sipariş Alındı</p>\n<h2>Sayın {{FIRSTNAME}} {{LASTNAME}},</h2>\n<p><strong>{{ORDER_NUMBER}}</strong> numaralı siparişiniz alınmıştır. BeArtShare'i tercih ettiğiniz için teşekkür ederiz.</p>\n{{PAYMENT_INFO_HTML}}\n{{ORDER_ITEMS_HTML}}\n<p>Herhangi bir sorunuz olması durumunda lütfen bizimle iletişime geçin!</p>",
        ],
        'admin_new_order' => [
            'name' => 'Yeni sipariş (yöneticilere)',
            'description' => 'Bildirim Alıcıları listesindeki yöneticilere giden SMS. E-postası sipariş detay tablosudur, düzenlenmez.',
            'email' => false,
            'variables' => [
                'ORDER_NUMBER' => 'Sipariş numarası', 'ORDER_ID' => 'Sipariş ID', 'CUSTOMER_NAME' => 'Müşteri', 'TOTAL' => 'Toplam tutar (TL)',
                'PAYMENT_METHOD' => 'Ödeme yöntemi', 'ARTWORK_TITLES' => 'Eser adları',
            ],
            'sms' => 'BeArtShare yeni sipariş: {{ORDER_NUMBER}} - {{CUSTOMER_NAME}} - {{TOTAL}} TL ({{PAYMENT_METHOD}}) - {{ARTWORK_TITLES}}',
        ],
        'password_reset' => [
            'name' => 'Şifre sıfırlama',
            'description' => '"Şifremi unuttum" ile istenen sıfırlama bağlantısı. Yalnızca e-posta.',
            'sms' => false,
            'variables' => [
                'FIRSTNAME' => 'Üyenin adı', 'LASTNAME' => 'Üyenin soyadı', 'RESET_URL' => 'Şifre sıfırlama bağlantısı', 'MINUTES' => 'Bağlantının geçerlilik süresi (dk)',
            ],
            'email_subject' => 'Şifre sıfırlama - BeArtShare',
            'email_body' => "<p class=\"label\">Şifre Sıfırlama</p>\n<h2>Merhaba,</h2>\n<p>Şifrenizi sıfırlamak için aşağıdaki butona tıklayın:</p>\n<p><a href=\"{{RESET_URL}}\" class=\"button\">Şifremi Sıfırla</a></p>\n<p>Bu bağlantı sizi yeni bir şifre oluşturma sayfasına yönlendirecek ve hesabınıza erişiminizi yeniden sağlayacaktır. Bağlantı {{MINUTES}} dakika geçerlidir.</p>\n<p>Eğer şifre sıfırlama talebinde bulunmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>\n<p class=\"muted\" style=\"font-size:11px;word-break:break-all;\">Buton çalışmazsa bu adresi tarayıcınıza yapıştırın:<br>{{RESET_URL}}</p>",
        ],
    ];

    /** Örnek değerler (admin önizleme) */
    public const SAMPLE = [
        'FIRSTNAME' => 'Ayşe', 'LASTNAME' => 'Yılmaz', 'ARTWORK_TITLE' => 'Mavi Kuşlar', 'ARTIST_NAME' => 'Nuri İyem',
        'OLD_PRICE' => '75.000', 'NEW_PRICE' => '65.000', 'ARTWORK_URL' => 'https://beartshare.com/eserler', 'ARTWORKS_URL' => 'https://beartshare.com/eserler',
        'ORDER_NUMBER' => 'ORD-6AC60DDA09646', 'ORDER_ID' => '197', 'ARTPUAN' => '650', 'ARTPUAN_BALANCE' => '1.300',
        'BUYER_FIRSTNAME' => 'Mehmet', 'DESCRIPTION' => 'Kampanya hediyesi', 'TOTAL' => '65.000', 'PAYMENT_CODE' => '89886000',
        'BANK_URL' => 'https://beartshare.com/banka-hesaplari', 'BANK_TRANSFER_SMS' => 'Ödeme tutarı: 65.000 TL. Havale açıklama kodunuz: 89886000.',
        'PAYMENT_INFO_HTML' => '<p class="note">Ödemenizi tamamlarken, açıklama kısmına özel transfer kodunuzu yazmayı unutmayınız.</p><div class="box">Ödenecek Tutar: <strong>65.000 TL</strong><br>Özel Transfer Kodunuz: <strong>89886000</strong></div>',
        'ORDER_ITEMS_HTML' => '<div class="box"><strong>Mavi Kuşlar</strong> · <span class="muted">Nuri İyem</span> · 65.000 TL</div>',
        'CUSTOMER_NAME' => 'Ayşe Yılmaz', 'PAYMENT_METHOD' => 'Havale / EFT', 'ARTWORK_TITLES' => 'Mavi Kuşlar',
        'RESET_URL' => 'https://beartshare.com/sifre-sifirla/ornek', 'MINUTES' => '60',
    ];

    /** Tanım + admin'deki değişiklik birleşmiş hâli */
    public static function get(string $key): array
    {
        $def = self::DEFINITIONS[$key] ?? throw new \InvalidArgumentException("Bilinmeyen bildirim şablonu: {$key}");
        $row = self::row($key);

        return [
            'key' => $key,
            'name' => $def['name'],
            'description' => $def['description'] ?? '',
            'variables' => $def['variables'] ?? [],
            'has_sms' => ($def['sms'] ?? false) !== false,
            'has_email' => ($def['email'] ?? null) !== false && isset($def['email_body']),
            'sms' => $row?->sms_body ?? (is_string($def['sms'] ?? null) ? $def['sms'] : null),
            'email_subject' => $row?->email_subject ?? ($def['email_subject'] ?? null),
            'email_body' => $row?->email_body ?? ($def['email_body'] ?? null),
            'sms_enabled' => $row ? $row->sms_enabled : true,
            'email_enabled' => $row ? $row->email_enabled : true,
            'customized' => (bool) $row,
        ];
    }

    /**
     * Şablonu doldurur. Dönen: sms (metin|null), subject, html (layout'lu e-posta|null).
     * Kapalı kanal null döner.
     */
    public static function render(string $key, array $vars): array
    {
        $t = self::get($key);

        return [
            'sms' => $t['has_sms'] && $t['sms_enabled'] && $t['sms'] ? self::fill($t['sms'], $vars, false) : null,
            'subject' => $t['email_subject'] ? self::fill($t['email_subject'], $vars, false) : null,
            'html' => $t['has_email'] && $t['email_enabled'] && $t['email_body'] ? self::layout(self::fill($t['email_body'], $vars, true)) : null,
        ];
    }

    /** {{DEĞİŞKEN}} → değer. E-postada değerler kaçışlanır; _HTML ile bitenler hazır HTML'dir. */
    public static function fill(string $text, array $vars, bool $html): string
    {
        $out = preg_replace_callback('/\{\{\s*([A-Z0-9_]+)\s*\}\}/', function ($m) use ($vars, $html) {
            $name = $m[1];
            if (!array_key_exists($name, $vars)) {
                return '';
            }
            $value = (string) $vars[$name];
            return ($html && !str_ends_with($name, '_HTML')) ? e($value) : $value;
        }, $text);

        // SMS'te boş değişkenden kalan fazla boşlukları topla
        return $html ? $out : trim(preg_replace('/[ \t]{2,}/', ' ', $out));
    }

    /**
     * Ortak e-posta görünümü (PDF tasarımı).
     * Görsel kullanılmaz: logo HTML metindir, resim engelleyen istemcilerde de görünür.
     * Gövdedeki sınıflar satır içi stile çevrilir: label, button, box, note, muted, price; <h2>, <p>.
     */
    public static function layout(string $body): string
    {
        $serif = "Georgia, 'Times New Roman', serif";
        $sans = "'Helvetica Neue', Helvetica, Arial, sans-serif";

        $body = strtr($body, [
            '<h2>' => "<h2 style=\"font-family:{$serif};font-size:24px;font-weight:normal;color:#14171c;margin:0 0 10px;\">",
            '<p>' => '<p style="margin:0 0 16px;">',
            'class="label"' => "style=\"font-family:{$sans};font-size:10px;letter-spacing:3px;text-transform:uppercase;color:#8a8a8a;margin:0 0 18px;\"",
            'class="button"' => "style=\"display:inline-block;background:#ae1c27;color:#ffffff;padding:13px 30px;text-decoration:none;font-family:{$sans};font-size:12px;letter-spacing:2px;text-transform:uppercase;border-radius:2px;\"",
            'class="box"' => 'style="background:#f9f7f3;border:1px solid #e6e1d8;padding:18px 20px;margin:0 0 20px;"',
            'class="note"' => 'style="border-left:3px solid #8b1a1a;padding:4px 0 4px 14px;color:#8b1a1a;margin:0 0 20px;"',
            'class="muted"' => 'style="color:#8a8a8a;"',
            'class="price"' => "style=\"font-family:{$serif};font-size:20px;color:#14171c;\"",
        ]);

        $logo = static fn (int $size) => "<table role=\"presentation\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0 auto;\"><tr><td style=\"font-family:{$sans};font-size:{$size}px;line-height:" . round($size * 0.86) . "px;color:#333333;letter-spacing:-0.5px;text-align:left;\">"
            . 'be<br><span style="padding-left:' . round($size * 0.38) . 'px;">art</span><br><span style="padding-left:' . round($size * 0.6) . 'px;">share</span></td></tr></table>';

        $logoTop = $logo(32);
        $logoFooter = $logo(28);

        return "<!DOCTYPE html><html lang=\"tr\"><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"></head>
<body style=\"margin:0;padding:0;background:#ffffff;\">
<table role=\"presentation\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background:#ffffff;\">
  <tr><td align=\"center\" style=\"padding:32px 16px 28px;\">
    <a href=\"https://beartshare.com\" style=\"text-decoration:none;\">{$logoTop}</a>
  </td></tr>
  <tr><td align=\"center\" style=\"padding:0 16px 40px;\">
    <table role=\"presentation\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width:520px;border:1px solid #e3e3e3;border-top:2px solid #14171c;background:#ffffff;\">
      <tr><td style=\"padding:36px 36px 32px;font-family:{$serif};font-size:14px;line-height:1.7;color:#4a4a4a;text-align:left;\">
        {$body}
      </td></tr>
    </table>
  </td></tr>
  <tr><td align=\"center\" style=\"background:#eef1f5;padding:36px 16px;font-family:{$sans};color:#333333;\">
    {$logoFooter}
    <p style=\"font-size:13px;margin:24px 0 6px;\">Harmancı Giz Plaza Harman Sokak No: 5 K: 21 D: 118, 34075, İstanbul</p>
    <p style=\"font-size:13px;margin:0 0 22px;\"><a href=\"tel:+905102216413\" style=\"color:#14171c;text-decoration:none;\">+90 510 221 64 13</a> &nbsp;&nbsp; <a href=\"mailto:info@beartshare.com\" style=\"color:#333333;text-decoration:none;\">info@beartshare.com</a></p>
    <p style=\"font-size:11px;letter-spacing:2px;text-transform:uppercase;margin:0 0 22px;\">
      <a href=\"https://www.instagram.com/beartshare\" style=\"color:#14171c;text-decoration:none;\">Instagram</a> &nbsp;·&nbsp;
      <a href=\"https://wa.me/905102216413\" style=\"color:#14171c;text-decoration:none;\">WhatsApp</a> &nbsp;·&nbsp;
      <a href=\"https://beartshare.com\" style=\"color:#14171c;text-decoration:none;\">beartshare.com</a>
    </p>
    <p style=\"font-size:11px;color:#777777;margin:0;\">Beartshare Online Sanat Galerisi A.Ş. &copy; " . date('Y') . "</p>
  </td></tr>
</table>
</body></html>";
    }

    /** Ad / soyad ayrımı (son kelime soyad) */
    public static function nameParts(?string $fullName): array
    {
        $parts = preg_split('/\s+/u', trim((string) $fullName)) ?: [];
        if (count($parts) <= 1) {
            return [$parts[0] ?? '', ''];
        }
        $last = array_pop($parts);
        return [implode(' ', $parts), $last];
    }

    /** Her çağrıda veritabanından okunur: kuyruk işçisi uzun yaşadığı için bellekte tutulmaz */
    protected static function row(string $key): ?NotificationTemplate
    {
        try {
            return NotificationTemplate::where('key', $key)->first();
        } catch (\Throwable) {
            return null; // tablo henüz yoksa (migrate öncesi) varsayılan metin
        }
    }
}
