<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // Genel
            [
                'question' => 'BeArtShare nedir?',
                'answer' => 'BeArtShare, Türkiye\'nin önde gelen online sanat galerisidir. Yerli ve yabancı sanatçıların özgün eserlerini güvenle satın alabileceğiniz, sanat severleri sanatçılarla buluşturan bir platformuz. Tüm eserlerimiz orijinal olup, orijinallik sertifikası ile teslim edilmektedir.',
                'category' => 'genel',
                'sort_order' => 1,
            ],
            [
                'question' => 'BeArtShare\'de satılan eserler orijinal mi?',
                'answer' => 'Evet, BeArtShare\'de satışa sunulan tüm eserler %100 orijinaldir. Her eser, sanatçının imzası ve BeArtShare orijinallik sertifikası ile birlikte teslim edilir. Reprodüksiyon veya kopya eser satışı yapılmamaktadır.',
                'category' => 'genel',
                'sort_order' => 2,
            ],
            [
                'question' => 'Sanatçı olarak BeArtShare\'de eserlerimi satabilir miyim?',
                'answer' => 'Evet, sanatçılar eserlerini BeArtShare\'de sergileyebilir ve satabilir. Bunun için web sitemizdeki "Eser Kabulu" sayfasından başvuru yapabilirsiniz. Uzman ekibimiz başvurunuzu değerlendirerek sizinle iletişime geçecektir.',
                'category' => 'genel',
                'sort_order' => 3,
            ],

            // Satın Alma
            [
                'question' => 'Nasıl sipariş verebilirim?',
                'answer' => 'Beğendiğiniz eseri sepetinize ekleyin, ardından ödeme sayfasına ilerleyin. Üye girişi yaparak veya üye olmadan misafir olarak alışveriş yapabilirsiniz. Teslimat adresinizi girdikten sonra ödeme yönteminizi seçerek siparişinizi tamamlayabilirsiniz.',
                'category' => 'satin-alma',
                'sort_order' => 1,
            ],
            [
                'question' => 'Satın almadan önce eseri görebilir miyim?',
                'answer' => 'Evet, randevu alarak galerimizde eserleri fiziksel olarak inceleyebilirsiniz. Ayrıca web sitemizdeki yüksek çözünürlüklü fotoğraflar ve detaylı açıklamalar sayesinde eserleri online olarak da detaylı inceleyebilirsiniz.',
                'category' => 'satin-alma',
                'sort_order' => 2,
            ],
            [
                'question' => 'Sepetimde bekleyen eser başkası tarafından satın alınabilir mi?',
                'answer' => 'Sepetinizdeki ürünler sizin için rezerve edilmez. Ödeme tamamlanana kadar eser başka bir müşteri tarafından satın alınabilir. Bu nedenle beğendiğiniz eseri mümkün olan en kısa sürede satın almanızı öneririz.',
                'category' => 'satin-alma',
                'sort_order' => 3,
            ],

            // Ödeme
            [
                'question' => 'Hangi ödeme yöntemlerini kullanabilirim?',
                'answer' => 'Kredi kartı (Visa, Mastercard) ve banka havalesi/EFT ile ödeme yapabilirsiniz. Kredi kartı ödemelerinde 3D Secure güvenlik sistemi kullanılmaktadır. Havale/EFT ödemelerinde sipariş, ödemenin hesabımıza geçmesinin ardından onaylanır.',
                'category' => 'odeme',
                'sort_order' => 1,
            ],
            [
                'question' => 'Taksitli ödeme yapabilir miyim?',
                'answer' => 'Evet, kredi kartı ile yapılan ödemelerde bankanızın sunduğu taksit seçeneklerinden yararlanabilirsiniz. Taksit seçenekleri ödeme sayfasında görüntülenir ve seçtiğiniz banka kartına göre değişiklik gösterebilir.',
                'category' => 'odeme',
                'sort_order' => 2,
            ],
            [
                'question' => 'Havale/EFT ile ödeme nasıl yapılır?',
                'answer' => 'Sipariş oluştururken "Havale/EFT" seçeneğini işaretleyin. Siparişinizin ardından banka hesap bilgilerimiz e-posta ile gönderilecektir. Açıklama kısmına sipariş numaranızı yazarak ödemenizi yapın. Ödemeniz kontrol edildikten sonra siparişiniz onaylanacaktır.',
                'category' => 'odeme',
                'sort_order' => 3,
            ],
            [
                'question' => 'Ödeme bilgilerim güvende mi?',
                'answer' => 'Kesinlikle evet. Tüm ödeme işlemleri 256-bit SSL şifreleme ile korunmaktadır. Kredi kartı bilgileriniz Garanti Bankası\'nın güvenli ödeme altyapısı üzerinden işlenir ve tarafımızca saklanmaz.',
                'category' => 'odeme',
                'sort_order' => 4,
            ],

            // Kargo ve Teslimat
            [
                'question' => 'Kargo ücreti ne kadar?',
                'answer' => 'Türkiye genelinde tüm siparişlerde kargo ücretsizdir. Yurtdışı gönderimler için teslimat adresi ve eserin boyutuna göre kargo ücreti hesaplanır ve ödeme aşamasında gösterilir.',
                'category' => 'kargo',
                'sort_order' => 1,
            ],
            [
                'question' => 'Siparişim ne zaman teslim edilir?',
                'answer' => 'Ödemenizin onaylanmasının ardından eserler özel olarak paketlenir ve 3-5 iş günü içerisinde kargoya verilir. Teslimat süresi bulunduğunuz şehre göre 1-3 iş günü arasında değişir. Siparişinizi "Hesabım" bölümünden takip edebilirsiniz.',
                'category' => 'kargo',
                'sort_order' => 2,
            ],
            [
                'question' => 'Eserler nasıl paketleniyor?',
                'answer' => 'Tüm eserler, taşıma sırasında zarar görmemesi için özel koruyucu malzemelerle profesyonelce paketlenir. Yağlı boya tablolar için köpük köşe koruyucuları ve hava kabarcıklı ambalaj kullanılır. Cam çerçeveli eserlerde ise ek koruma önlemleri alınır.',
                'category' => 'kargo',
                'sort_order' => 3,
            ],
            [
                'question' => 'Yurtdışına gönderim yapıyor musunuz?',
                'answer' => 'Evet, dünya genelinde birçok ülkeye gönderim yapıyoruz. Yurtdışı siparişlerinde gümrük vergileri ve ek masraflar alıcıya aittir. Detaylı bilgi için bizimle iletişime geçebilirsiniz.',
                'category' => 'kargo',
                'sort_order' => 4,
            ],

            // İade ve İptal
            [
                'question' => 'Siparişimi iptal edebilir miyim?',
                'answer' => 'Kargoya verilmemiş siparişlerinizi iptal edebilirsiniz. Bunun için "Hesabım" bölümünden veya müşteri hizmetlerimizle iletişime geçerek iptal talebinde bulunabilirsiniz. Kargoya verilen siparişler için iade prosedürü uygulanır.',
                'category' => 'iade',
                'sort_order' => 1,
            ],
            [
                'question' => 'İade koşulları nelerdir?',
                'answer' => 'Teslim tarihinden itibaren 14 gün içinde, eserin orijinal ambalajında ve hasarsız olması koşuluyla iade kabul edilmektedir. İade talebinizi müşteri hizmetlerimize bildirmeniz gerekmektedir. Özel sipariş veya kişiye özel üretilen eserlerde iade kabul edilmemektedir.',
                'category' => 'iade',
                'sort_order' => 2,
            ],
            [
                'question' => 'İade durumunda ücret iadesi nasıl yapılır?',
                'answer' => 'İade edilen eserin tarafımıza ulaşıp kontrol edilmesinin ardından, ödemeniz aynı yöntemle 5-10 iş günü içerisinde iade edilir. Kredi kartı ödemelerinde iade süresi bankanıza göre değişiklik gösterebilir.',
                'category' => 'iade',
                'sort_order' => 3,
            ],

            // Üyelik
            [
                'question' => 'Üye olmadan alışveriş yapabilir miyim?',
                'answer' => 'Hayır, alışveriş yapabilmek için üye olmanız gerekmektedir. Üyelik sayesinde siparişlerinizi takip edebilir, favori listesi oluşturabilir ve ArtPuan kazanabilirsiniz.',
                'category' => 'uyelik',
                'sort_order' => 1,
            ],
            [
                'question' => 'Şifremi unuttum, ne yapmalıyım?',
                'answer' => 'Giriş sayfasındaki "Şifremi Unuttum" bağlantısına tıklayarak e-posta adresinizi girin. Size şifre sıfırlama bağlantısı göndereceğiz. Bu bağlantı üzerinden yeni şifrenizi belirleyebilirsiniz.',
                'category' => 'uyelik',
                'sort_order' => 2,
            ],
            [
                'question' => 'Hesabımı nasıl silebilirim?',
                'answer' => 'Hesap silme talebiniz için müşteri hizmetlerimizle iletişime geçmeniz gerekmektedir. Aktif siparişiniz veya iade süreciniz yoksa hesabınız kalıcı olarak silinecektir.',
                'category' => 'uyelik',
                'sort_order' => 3,
            ],

            // ArtPuan
            [
                'question' => 'ArtPuan nedir?',
                'answer' => 'ArtPuan, BeArtShare\'de yaptığınız alışverişlerden kazandığınız sadakat puanlarıdır. Her 100 TL\'lik alışverişinizde 1 ArtPuan kazanırsınız. Biriktirdiğiniz puanları sonraki alışverişlerinizde indirim olarak kullanabilirsiniz.',
                'category' => 'artpuan',
                'sort_order' => 1,
            ],
            [
                'question' => 'ArtPuan nasıl kazanılır?',
                'answer' => 'ArtPuan kazanmanın birkaç yolu vardır: Alışveriş yaparak (her 100 TL = 1 puan), arkadaşlarınızı davet ederek (davet edilen kişinin ilk alışverişinde bonus puan) ve özel kampanyalara katılarak puan kazanabilirsiniz.',
                'category' => 'artpuan',
                'sort_order' => 2,
            ],
            [
                'question' => 'ArtPuan nasıl kullanılır?',
                'answer' => 'Ödeme sayfasında "ArtPuan Kullan" seçeneğini işaretleyerek puanlarınızı kullanabilirsiniz. 1 ArtPuan = 1 TL değerindedir. Puanlarınızın tamamını veya bir kısmını kullanabilirsiniz.',
                'category' => 'artpuan',
                'sort_order' => 3,
            ],
            [
                'question' => 'ArtPuan\'ların geçerlilik süresi var mı?',
                'answer' => 'Evet, kazanılan ArtPuanlar 12 ay süreyle geçerlidir. Son kullanma tarihi yaklaşan puanlarınız hakkında e-posta ile bilgilendirilirsiniz.',
                'category' => 'artpuan',
                'sort_order' => 4,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create(array_merge($faq, ['is_active' => true]));
        }
    }
}
