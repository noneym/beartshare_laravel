<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Admin listelerinde tablo başlığına tıklayarak sıralama: ?sort=<anahtar>&dir=asc|desc
 * Başlık tarafı: <x-admin.th sort="anahtar">Etiket</x-admin.th>
 */
trait SortsIndex
{
    /**
     * @param array<string, string|\Closure> $columns anahtar => sütun adı veya fn (Builder $q, string $dir)
     * @return bool sıralama uygulandıysa true (uygulanmadıysa çağıran kendi varsayılanını kullanır)
     */
    protected function applySort(Builder $query, Request $request, array $columns): bool
    {
        $key = $request->query('sort');
        if (!is_string($key) || !isset($columns[$key]) || !$request->filled('dir')) {
            return false;
        }

        $dir = $request->query('dir') === 'asc' ? 'asc' : 'desc';
        $column = $columns[$key];

        if ($column instanceof \Closure) {
            $column($query, $dir);
        } else {
            $query->orderBy($column, $dir);
        }

        // Eşit değerlerde sabit sıra
        $query->orderBy($query->getModel()->getQualifiedKeyName(), 'desc');

        return true;
    }
}
