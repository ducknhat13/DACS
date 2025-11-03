<?php

namespace App\Console\Commands;

use App\Models\Poll;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseExpiredPolls extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'polls:auto-close';

    /**
     * The console command description.
     */
    protected $description = 'Đóng tất cả các poll đã tới thời điểm auto_close_at nhưng chưa được đóng';

    public function handle(): int
    {
        $now = Carbon::now();

        $affected = Poll::query()
            ->whereNull('deleted_at')
            ->where('is_closed', false)
            ->whereNotNull('auto_close_at')
            ->where('auto_close_at', '<=', $now)
            ->update(['is_closed' => true]);

        $this->info("Closed {$affected} expired polls at {$now->toDateTimeString()}");
        return self::SUCCESS;
    }
}


