<?php /*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
| - Định nghĩa các Artisan commands closure-based (nếu cần).
| - Với Laravel 11, lịch Scheduler được cấu hình trong bootstrap/app.php.
*/ ?>
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
