<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Kuyruk durumu: toplu gönderim batch'leri, bekleyen / hatalı işler, worker ve kur durumu.
 */
class QueueController extends Controller
{
    public const HEARTBEAT_KEY = 'queue:worker:heartbeat';

    public function index(Request $request)
    {
        $batches = DB::table('job_batches')->orderByDesc('created_at')->limit(30)->get()
            ->map(function ($b) {
                $done = $b->total_jobs - $b->pending_jobs;
                $b->processed = $done;
                $b->progress = $b->total_jobs ? (int) round($done / $b->total_jobs * 100) : 100;
                $b->running = !$b->finished_at && !$b->cancelled_at && $b->pending_jobs > 0;
                return $b;
            });

        $jobs = DB::table('jobs')->orderBy('id')->limit(50)->get()->map(function ($j) {
            $j->name = $this->displayName($j->payload);
            return $j;
        });

        $failed = DB::table('failed_jobs')->orderByDesc('failed_at')->limit(50)->get()->map(function ($f) {
            $f->name = $this->displayName($f->payload);
            $f->error = strtok((string) $f->exception, "\n");
            return $f;
        });

        $heartbeat = Cache::get(self::HEARTBEAT_KEY);

        $stats = [
            'pending' => DB::table('jobs')->whereNull('reserved_at')->count(),
            'running' => DB::table('jobs')->whereNotNull('reserved_at')->count(),
            'failed' => DB::table('failed_jobs')->count(),
            'worker_alive' => $heartbeat && now()->timestamp - $heartbeat < 120,
            'heartbeat' => $heartbeat,
        ];

        $rates = ExchangeRate::latest('id')->limit(10)->get();
        $highlight = $request->query('batch');

        return view('admin.queue.index', compact('batches', 'jobs', 'failed', 'stats', 'rates', 'highlight'));
    }

    public function retry(Request $request)
    {
        $id = $request->input('uuid', 'all');
        Artisan::call('queue:retry', ['id' => [$id]]);

        return back()->with('success', $id === 'all' ? 'Tüm hatalı işler tekrar kuyruğa alındı.' : 'İş tekrar kuyruğa alındı.');
    }

    public function forget(Request $request)
    {
        if ($request->input('uuid') === 'all') {
            Artisan::call('queue:flush');
            return back()->with('success', 'Tüm hatalı iş kayıtları silindi.');
        }

        Artisan::call('queue:forget', ['id' => $request->input('uuid')]);
        return back()->with('success', 'Hatalı iş kaydı silindi.');
    }

    public function cancelBatch(string $id)
    {
        $batch = Bus::findBatch($id);
        if ($batch && !$batch->finished()) {
            $batch->cancel();
            // Henüz başlamamış işleri de kuyruktan temizle
            DB::table('jobs')->where('payload', 'like', '%' . $id . '%')->whereNull('reserved_at')->delete();
        }

        return back()->with('success', 'Gönderim iptal edildi.');
    }

    public function updateRates()
    {
        $code = Artisan::call('rates:update');
        $output = trim(Artisan::output());

        return back()->with($code === 0 ? 'success' : 'error', $output ?: 'Kur güncellenemedi.');
    }

    protected function displayName(string $payload): string
    {
        $data = json_decode($payload, true);
        return class_basename($data['displayName'] ?? 'Job');
    }
}
