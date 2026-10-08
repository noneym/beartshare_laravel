<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

/**
 * Değişen slug'ların eski adresleri: /eser/{eski} → 301 → /eser/{yeni}.
 * Bir kaydın slug'ı her değiştiğinde (renameSlug) eski değer buraya yazılır.
 */
class SlugRedirect extends Model
{
    protected $fillable = ['model', 'old_slug', 'model_id'];

    /** Model sınıfı → tablodaki kısa anahtar */
    public static function keyFor(string $class): string
    {
        return match ($class) {
            Artwork::class => 'artwork',
            Artist::class => 'artist',
            BlogPost::class => 'blog_post',
            default => throw new \InvalidArgumentException("Slug yönlendirmesi desteklenmiyor: {$class}"),
        };
    }

    /** Eski slug'ı kaydeder; yeni slug daha önce "eski" olarak kayıtlıysa döngü olmasın diye silinir. */
    public static function record(string $class, string $oldSlug, int $modelId, string $newSlug): void
    {
        $key = static::keyFor($class);

        static::where('model', $key)->where('old_slug', $newSlug)->delete();
        static::updateOrCreate(['model' => $key, 'old_slug' => $oldSlug], ['model_id' => $modelId]);
    }

    /** Eski slug başka bir adrese taşındıysa hedef kaydı döndürür. */
    public static function target(string $class, string $slug): ?Model
    {
        $id = static::where('model', static::keyFor($class))->where('old_slug', $slug)->value('model_id');

        return $id ? $class::find($id) : null;
    }

    /** Eski slug'ın kullanımda mı (yeni kayıtlar eski adresleri devralmasın) */
    public static function exists(string $class, string $slug): bool
    {
        return static::where('model', static::keyFor($class))->where('old_slug', $slug)->exists();
    }

    /** Taşınmış slug ise yeni adrese 301 ile yönlendirir, değilse 404. */
    public static function redirectOr404(string $class, string $slug, string $route): never
    {
        $target = static::target($class, $slug);

        if ($target) {
            // redirect() yardımcısı Livewire bileşeni içinde Livewire'ın Redirector'ını döndürür; yanıtı doğrudan kur
            throw new HttpResponseException(new RedirectResponse(route($route, $target->slug), 301));
        }

        abort(404);
    }
}
