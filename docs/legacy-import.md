# Eski Sistemden Aktarım Senaryosu

Eski CodeIgniter sisteminin (`C:\projects\beartshareBackend`, MariaDB) tüm verisi
`php artisan legacy:import` komutu ile yeni Laravel şemasına aktarılır.
Komut kaynak tabloları salt okunur kullanır; eski sistem çalışmaya devam edebilir.

## Bağlantı

`.env` (repoya girmez):

```
OLD_DB_HOST=...
OLD_DB_PORT=...
OLD_DB_DATABASE=beartshare
OLD_DB_USERNAME=...
OLD_DB_PASSWORD=...
```

`config/database.php` → `legacy` bağlantısı.

## Adımlar

1. **Prova (önerilir)** — boş bir yerel veritabanında:
   ```
   DB_HOST=127.0.0.1 DB_PORT=3307 DB_DATABASE=bas_rehearsal DB_USERNAME=root DB_PASSWORD= \
     php artisan legacy:import --fresh --force
   ```
   `storage/app/legacy-import-artpuan.csv` raporunu ve çıktıdaki uyarıları kontrol et.
2. **Gerçek aktarım**:
   ```
   php artisan legacy:import --fresh --images
   ```
   - Önce hedef veritabanının tam JSON yedeği alınır: `storage/app/db-backups/pre-legacy-import-*.json`
   - `migrate:fresh` ile tüm tablolar sıfırlanır.
   - Yeni sistemde yönetilen tablolar yedekten geri yüklenir: `categories, faqs, static_pages, art_terms, blog_categories`
   - Aktarım yapılır, ardından `images:migrate` eşlemesi olmayan görselleri R2'ye taşır.
3. **Canlıya geçiş günü**: eski sistemi salt okunur moda al (yeni sipariş alma), komutu tekrar çalıştır
   (`--fresh --images --backup=<ilk yedek>` — ilk yedek, yeni sistemde girilmiş kategori/konsinye/öne çıkan bilgilerinin kaynağıdır),
   sonra DNS'i yeni sisteme çevir.

Komut tekrar çalıştırılabilir: `--fresh` her seferinde sıfırdan kurar. R2'ye taşınmış görseller
`storage/app/image-url-map.json` eşlemesi sayesinde yeniden indirilmez.

## Eşleme Kuralları

| Eski | Yeni | Not |
|---|---|---|
| `users` | `users` | id korunur. Ad + soyad birleşir. Telefon `905XXXXXXXXX` → `5XXXXXXXXX`. Aynı e-postalı ikinci hesap ilkine bağlanır; çakışan telefon/TC boş bırakılır. |
| `users.password` (tuzsuz SHA1) | `bcrypt(sha1)` + `legacy_password=1` | İlk başarılı girişte normal bcrypt'e yükseltilir (`User::checkPassword`). Yeni sistemde hesabı olan admin'in yeni şifresi korunur. |
| `ref_users` | `users.referred_by` | Kişi başına ilk kayıt. |
| `user_address` | `addresses` | id korunur, ilk adres varsayılan. Türkiye dışı ülke adrese eklenir. |
| `artists` | `artists` | id korunur. `born_date/death_date` → yıl. |
| `products` + `product_detail (tr)` | `artworks` | id korunur. `canvas_type` → teknik, `canvas_size` → ölçü, `basket_description` → `sale_note` (eser sayfasında fiyat altında), `passive` → `is_active=0`, `whosale` → `wholesale`. Fiyat KDV dahil tam TL; USD güncel TCMB satış kuruyla. Sanatçısı olmayan eski test ürünleri atlanır. Kategori / konsinye sahibi / admin notu / öne çıkan / kredi kartı izni önceki yeni sistem kaydından (old_id) alınır, yoksa kategori teknikten tahmin edilir. |
| `product_favorites` | `favorites` | |
| `basket` | `cart_items` | Yalnızca satılmamış, aktif eserler. |
| `orders` + `order_products` + `payments` | `orders`, `order_items`, `payment_transactions` | id korunur. Sipariş no = `uniq_id`. Havale kodu = `1050{id}` (eski sistemdeki açıklama kodu). Toplam = Σ fiyat − kullanılan puan. Mükerrer ödeme kaydı (çift onay) tek sayılır. Kalem USD'si sipariş tarihindeki kurla. |
| `order_notes`, `shipment_info` | `orders.admin_notes`, `shipping_company`, `tracking_number` | Admin sipariş detayında "İşlem Geçmişi" ve "Kargo". |
| `blog_post` | `blog_posts` | id korunur. İngilizce kategorilerdeki (4, 6, 8) yazılar pasif. |
| `points` + `ref_point_cash_out` | `art_puan_logs` + `users.art_puan` | Aşağıya bakın. |

Sipariş durumları: `awaiting→pending`, `success→confirmed`, `shipped`, `delivered`, `cancelled`, `returned`, `refunded`.

Aktarılmayanlar: oturumlar, doğrulama/şifre sıfırlama token'ları, `users_log`, `exchange_rates` (yalnızca kur için okunur),
`lang_values`, `email_templates`, `agreements`, il/ilçe tabloları, boş tablolar (`ticket*`, `bank_accounts`, `product_share`).

## Tarihler

Eski sistemde PHP `Europe/Istanbul` ile yazdığı tarihler yerel saat metinleri; MySQL varsayılanıyla dolan
`timestamp` sütunları (`shipment_info`, `users_log`, `exchange_rates`) ise sunucu saatinde (UTC).
Yeni sistem artık `Europe/Istanbul` ile çalışır (`APP_TIMEZONE`, `DB_TIMEZONE=+03:00`); yerel metinler olduğu gibi,
UTC olanlar +3 saat çevrilerek yazılır. `0000-00-00` değerleri boş kabul edilir.

## ArtPuan — Eski Sistemdeki Mantık Hataları ve Yeni Defter

Eski sistem:

- Kazanım `points` tablosuna, harcama `ref_point_cash_out` tablosuna yazılıyor; bakiye = Σpoints − Σcash_out.
- `points.status` hiç yazılmıyor (hep 0). Bazı sorgular `status = 'active'` diye filtreliyor; sayısal sütunda
  `'active'` 0'a dönüştüğü için "0 = aktif" gibi çalışıyor. Yani 0 hem "varsayılan" hem "aktif" anlamında.
- `ref_point_cash_out.status`: 1 = checkout'ta ayrıldı, 0 = admin onayında kesinleşti (yani 0 burada "aktif değil" demek değil).
  Bakiye hesabı ise status'a hiç bakmıyor; geçmiş listesi bakıyor → liste ile toplam tutarsız.
- Hatalar: onay iki kez verilince puan/ödeme mükerrer yazılıyor; iptal edilen siparişin kazandırdığı puan geri alınmıyor;
  2024-10 – 2025-01 arası harcama iki kez düşülmüş (ör. sipariş #73); tamamen puanla ödenen eski siparişlerde harcama
  admin'e / var olmayan kullanıcıya (#1888) yazılmış; bakiye kontrolü atlanabildiği için bakiyeden fazla harcama mümkün.

Yeni defter siparişlerden yeniden kurulur (`--keep-cancelled-earnings` ile iptal kazanımları korunabilir):

1. Kazanım: alış %1 ve referans satırları; aynı sipariş için mükerrerler tek; iptal/iade (`cancelled`, `returned`,
   `refunded`) edilmiş siparişlerin kazanımları alınmaz.
2. Kampanya/hediye/satıcı puanları olduğu gibi (`bonus`, diğerleri `manual`).
3. Harcama: her siparişin `points_used` değeri, sipariş tarihinde, sipariş sahibinden. O tarihteki bakiyeden fazlası
   düşülmez (eksi bakiye oluşmaz; açıklamada belirtilir).
4. İade: iptal/iade edilmiş siparişte düşülen puan geri verilir.
5. Kronolojik `balance_after`; son bakiye `users.art_puan`.

Eski bakiye ile fark olan kullanıcılar `storage/app/legacy-import-artpuan.csv` raporunda listelenir.
