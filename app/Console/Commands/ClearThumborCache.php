<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Thumbor result storage bucket'ını (küçültülmüş kopyalar) boşaltır.
 * Dosyalar R2'deki orijinallerden ilk istekte yeniden üretilir.
 */
class ClearThumborCache extends Command
{
    protected $signature = 'images:clear-cache {--bucket=beartsharecache : Thumbor result storage bucket\'ı}';

    protected $description = 'Thumbor önbellek bucket\'ını boşaltır (aynı R2 hesap bilgileriyle)';

    public function handle(): int
    {
        $bucket = $this->option('bucket');
        config(['filesystems.disks.thumbor_cache' => array_merge(config('filesystems.disks.r2'), ['bucket' => $bucket])]);
        $disk = Storage::disk('thumbor_cache');

        $files = [];
        $bytes = 0;
        foreach ($disk->getDriver()->listContents('', true) as $item) {
            if ($item->isFile()) {
                $files[] = $item->path();
                $bytes += $item->fileSize();
            }
        }

        if (!$files) {
            $this->info("{$bucket} zaten boş.");
            return self::SUCCESS;
        }

        if (!$this->confirm(sprintf('%s bucket\'ından %d dosya (%.0f MB) kalıcı olarak silinecek. Devam?', $bucket, count($files), $bytes / 1048576))) {
            return self::SUCCESS;
        }

        foreach (array_chunk($files, 500) as $chunk) {
            $disk->delete($chunk);
        }

        $this->info(count($files) . ' önbellek dosyası silindi.');

        return self::SUCCESS;
    }
}
