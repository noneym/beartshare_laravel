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
            'email_body' => "<p>Merhaba {{FIRSTNAME}},</p>\n<p>Favorilerinizdeki <strong>“{{ARTWORK_TITLE}}”</strong> ({{ARTIST_NAME}}) başlıklı eserin fiyatı <strong>{{OLD_PRICE}} TL</strong>'den <strong>{{NEW_PRICE}} TL</strong>'ye güncellendi.</p>\n<p style=\"text-align:center;margin:28px 0;\"><a href=\"{{ARTWORK_URL}}\" class=\"button\">Eseri İncele</a></p>",
        ],
        'favorite_reserved' => [
            'name' => 'Favori eser rezerve edildi',
            'description' => 'Bir üye eseri sepetten satın almaya başladığında (havale siparişi) eseri favorisine eklemiş diğer üyelere gider. Aynı üyeye aynı eser için bir kez gider.',
            'variables' => [
                'FIRSTNAME' => 'Üyenin adı', 'LASTNAME' => 'Üyenin soyadı', 'ARTWORK_TITLE' => 'Eser adı', 'ARTIST_NAME' => 'Sanatçı', 'ARTWORK_URL' => 'Eser sayfası adresi',
            ],
            'sms' => 'Favorilerinizdeki “{{ARTWORK_TITLE}}“ başlıklı eser başka bir koleksiyoner tarafından rezerve edildi. Eseri incelemek için: beartshare.com',
            'email_subject' => 'Favori eseriniz rezerve edildi',
            'email_body' => "<p>Merhaba {{FIRSTNAME}},</p>\n<p>Favorilerinizdeki <strong>“{{ARTWORK_TITLE}}”</strong> ({{ARTIST_NAME}}) başlıklı eser başka bir koleksiyoner tarafından rezerve edildi.</p>\n<p style=\"text-align:center;margin:28px 0;\"><a href=\"{{ARTWORK_URL}}\" class=\"button\">Eseri İncele</a></p>",
        ],
        'favorite_sold' => [
            'name' => 'Favori eser satıldı',
            'description' => 'Eser satıldığında (sipariş onaylanınca ya da admin "Satıldı" işaretleyince) eseri favorisine eklemiş diğer üyelere gider. Aynı üyeye aynı eser için bir kez gider.',
            'variables' => [
                'FIRSTNAME' => 'Üyenin adı', 'LASTNAME' => 'Üyenin soyadı', 'ARTWORK_TITLE' => 'Eser adı', 'ARTIST_NAME' => 'Sanatçı', 'ARTWORKS_URL' => 'Eserler sayfası adresi',
            ],
            'sms' => 'Favorilerinizdeki “{{ARTWORK_TITLE}}“ başlıklı eser başka bir koleksiyoner tarafından satın alındı. Yeni eserleri keşfetmek için: beartshare.com',
            'email_subject' => 'Favori eseriniz satıldı',
            'email_body' => "<p>Merhaba {{FIRSTNAME}},</p>\n<p>Favorilerinizdeki <strong>“{{ARTWORK_TITLE}}”</strong> ({{ARTIST_NAME}}) başlıklı eser başka bir koleksiyoner tarafından satın alındı.</p>\n<p>Yeni eserleri keşfetmek için koleksiyonumuza göz atabilirsiniz.</p>\n<p style=\"text-align:center;margin:28px 0;\"><a href=\"{{ARTWORKS_URL}}\" class=\"button\">Eserleri Keşfet</a></p>",
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
            'email_body' => "<p>Sayın {{FIRSTNAME}} {{LASTNAME}},</p>\n<p><strong>{{ORDER_NUMBER}}</strong> numaralı eser alımınız onaylandı. Bu alımınızla <strong>{{ARTPUAN}} ArtPuan®</strong> kazandınız. Bir sonraki eser alımınızda ArtPuan®’larınızı kullanabilirsiniz.</p>\n<p>Toplam ArtPuan® bakiyeniz: <strong>{{ARTPUAN_BALANCE}}</strong></p>\n<p>Bizi tercih ettiğiniz için teşekkür ederiz!</p>",
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
            'email_body' => "<p>Merhaba {{FIRSTNAME}},</p>\n<p>Referansınız <strong>{{BUYER_FIRSTNAME}}</strong> bir eser alımı gerçekleştirdi ve siz <strong>{{ARTPUAN}} ArtPuan®</strong> kazandınız.</p>\n<p>Toplam ArtPuan® bakiyeniz: <strong>{{ARTPUAN_BALANCE}}</strong></p>",
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
            'email_body' => "<p>Merhaba {{FIRSTNAME}},</p>\n<p>Hesabınıza <strong>{{ARTPUAN}} ArtPuan®</strong> eklendi.</p>\n<p><strong>Açıklama:</strong> {{DESCRIPTION}}</p>\n<p>Toplam ArtPuan® bakiyeniz: <strong>{{ARTPUAN_BALANCE}}</strong>. ArtPuan®’larınızı sonraki eser alımlarınızda kullanabilirsiniz.</p>",
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
            'email_body' => "<p>Merhaba {{FIRSTNAME}},</p>\n<p><strong>{{ORDER_NUMBER}}</strong> numaralı siparişiniz başarıyla oluşturuldu. Teşekkür ederiz!</p>\n{{PAYMENT_INFO_HTML}}\n{{ORDER_ITEMS_HTML}}",
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
            'email_body' => "<p>Merhaba {{FIRSTNAME}},</p>\n<p>Hesabınız için bir şifre sıfırlama talebi aldık. Yeni şifrenizi belirlemek için aşağıdaki butona tıklayın.</p>\n<p style=\"text-align:center;margin:28px 0;\"><a href=\"{{RESET_URL}}\" class=\"button\">Şifremi Sıfırla</a></p>\n<p style=\"color:#888;font-size:12px;\">Bu bağlantı {{MINUTES}} dakika geçerlidir. Bu talebi siz yapmadıysanız bu e-postayı yok sayabilirsiniz; şifreniz değişmez.</p>\n<p style=\"color:#aaa;font-size:11px;word-break:break-all;\">Buton çalışmazsa bu adresi tarayıcınıza yapıştırın:<br>{{RESET_URL}}</p>",
        ],
    ];

    /** Örnek değerler (admin önizleme) */
    public const SAMPLE = [
        'FIRSTNAME' => 'Ayşe', 'LASTNAME' => 'Yılmaz', 'ARTWORK_TITLE' => 'Mavi Kuşlar', 'ARTIST_NAME' => 'Nuri İyem',
        'OLD_PRICE' => '75.000', 'NEW_PRICE' => '65.000', 'ARTWORK_URL' => 'https://beartshare.com/eserler', 'ARTWORKS_URL' => 'https://beartshare.com/eserler',
        'ORDER_NUMBER' => 'ORD-6AC60DDA09646', 'ORDER_ID' => '197', 'ARTPUAN' => '650', 'ARTPUAN_BALANCE' => '1.300',
        'BUYER_FIRSTNAME' => 'Mehmet', 'DESCRIPTION' => 'Kampanya hediyesi', 'TOTAL' => '65.000', 'PAYMENT_CODE' => '89886000',
        'BANK_URL' => 'https://beartshare.com/banka-hesaplari', 'BANK_TRANSFER_SMS' => 'Ödeme tutarı: 65.000 TL. Havale açıklama kodunuz: 89886000.',
        'PAYMENT_INFO_HTML' => '<p style="background:#fefce8;border:1px solid #fde68a;padding:12px;text-align:center;">Havale açıklama kodunuz: <strong>89886000</strong></p>',
        'ORDER_ITEMS_HTML' => '<p style="background:#f8f8f8;padding:12px;">Mavi Kuşlar · Nuri İyem · 65.000 TL</p>',
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

    /** Ortak e-posta görünümü */
    public static function layout(string $body): string
    {
        $year = date('Y');
        $body = str_replace('class="button"', 'style="display:inline-block;background:#14171c;color:#fff;padding:12px 32px;text-decoration:none;font-size:14px;"', $body);

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #14171c; padding: 24px; text-align: center;'>
                <h1 style='color: #fff; font-size: 20px; margin: 0;'>BeArtShare</h1>
            </div>
            <div style='padding: 32px 24px; background: #fff; color: #444; font-size: 14px; line-height: 1.6;'>
                {$body}
            </div>
            <div style='padding: 16px 24px; background: #f8f8f8; text-align: center;'>
                <p style='color: #999; font-size: 11px; margin: 0;'>Beartshare Online Sanat Galerisi A.Ş. &copy; {$year} · <a href='https://beartshare.com' style='color:#999;'>beartshare.com</a></p>
            </div>
        </div>";
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
