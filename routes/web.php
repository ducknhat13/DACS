<?php

/**
 * Web Routes - Định nghĩa các routes cho web application
 * 
 * Cấu trúc routes:
 * - Public routes: Home, About, Contact, Terms, Privacy
 * - Auth routes: Login, Register, Password reset (trong auth.php)
 * - Protected routes (auth middleware): Dashboard, Profile, Poll creation
 * - Poll routes: 
 *   + Public: Vote, View results, Quick access
 *   + Protected: Create, Toggle, Export, Delete
 * - API routes: Media upload (bypass CSRF)
 * 
 * @author QuickPoll Team
 */

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\ContactController;
use App\Models\Poll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\StatsController;
use Illuminate\Http\Request;
use App\Models\User;

// ============================================
// PUBLIC ROUTES - Không cần authentication
// ============================================

// Home page
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Test email route (only in local environment)
if (app()->environment('local')) {
    Route::get('/test-email', function () {
        try {
            $testEmail = Auth::check() ? Auth::user()->email : 'test@example.com';
            
            Mail::raw('Đây là email test từ QuickPoll. Nếu bạn nhận được email này, cấu hình SMTP đã hoạt động đúng!', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email - QuickPoll');
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Email đã được gửi đến: ' . $testEmail,
                'note' => 'Vui lòng kiểm tra inbox (và cả spam folder)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'hint' => 'Kiểm tra lại cấu hình MAIL_* trong file .env'
            ], 500);
        }
    })->middleware('auth')->name('test.email');
}

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// Switch locale
Route::get('/locale/{lang}', function($lang){
    if (!in_array($lang, ['vi','en'])) { $lang = 'vi'; }
    Session::put('locale', $lang);
    if (Auth::check()) {
        $user = Auth::user();
        $user->locale = $lang;
        $user->save();
        auth()->setUser($user->fresh());
    }
    return back();
})->name('locale.switch');

Route::get('/dashboard', function () {
    $polls = collect();
    if (Auth::check()) {
        $query = Poll::with(['options' => function ($q) { $q->withCount('votes'); }])
            ->withCount('votes')
            ->where('user_id', Auth::id());

        // Search filters
        $q = request('q');
        $status = request('status'); // open|closed|all
        $sort = request('sort'); // newest|votes

        if ($q) {
            $query->where(function($qq) use ($q){
                $qq->where('question', 'like', "%{$q}%")
                   ->orWhere('slug', 'like', "%{$q}%");
            });
        }
        if ($status === 'open') { $query->where('is_closed', false); }
        if ($status === 'closed') { $query->where('is_closed', true); }

        if ($sort === 'votes') { $query->orderByDesc('votes_count'); }
        else { $query->latest('id'); }

        $polls = $query->get();
    }
    return view('dashboard', ['polls' => $polls]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Stats / History page
Route::get('/history', [StatsController::class, 'index'])->middleware('auth')->name('stats.index');

Route::middleware('auth')->group(function () {
    Route::post('/profile/locale', [ProfileController::class, 'updateLocale'])->name('profile.locale');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================
// POLL ROUTES
// ============================================

// Poll creation (chỉ authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/polls/create', [PollController::class, 'create'])->name('polls.create');
    Route::post('/polls', [PollController::class, 'store'])->name('polls.store');
});

// Media upload routes (bypass CSRF cho API calls từ frontend)
Route::prefix('api')->group(function () {
    Route::post('/media/upload', [ImageUploadController::class, 'upload'])->name('media.upload');
    Route::post('/media/validate-url', [ImageUploadController::class, 'validateUrl'])->name('media.validate-url');
    Route::delete('/media/delete', [ImageUploadController::class, 'delete'])->name('media.delete');
});

// Quick Access Route - Truy cập nhanh poll bằng slug từ header
Route::get('/quick-access/{code}', [PollController::class, 'quickAccess'])->name('polls.quick-access');

// Private poll access (nhập access key)
Route::get('/polls/{slug}/access', [PollController::class, 'accessForm'])->name('polls.access');
Route::post('/polls/{slug}/access', [PollController::class, 'accessCheck'])->name('polls.access.check');

// Poll viewing/voting routes (có middleware EnsurePollAccess để check private polls)
Route::middleware(\App\Http\Middleware\EnsurePollAccess::class)->group(function () {
    // Guest name capture (cho private polls yêu cầu tên)
    Route::get('/polls/{slug}/name', [\App\Http\Controllers\PollController::class, 'nameForm'])->name('polls.name');
    Route::post('/polls/{slug}/name', [\App\Http\Controllers\PollController::class, 'saveName'])->name('polls.name.save');
    
    // Vote page và Results page (public, nhưng phải qua access check nếu private)
    Route::get('/polls/{slug}', [PollController::class, 'vote'])->name('polls.vote');
    Route::get('/polls/{slug}/results', [PollController::class, 'show'])->name('polls.show');
    
    // Submit vote và comment (public)
    Route::post('/polls/{slug}/vote', [VoteController::class, 'store'])->name('polls.vote.store');
    Route::post('/polls/{slug}/comments', [PollController::class, 'comment'])->name('polls.comment');
});

// Poll management routes (chỉ owner mới được)
Route::middleware('auth')->group(function () {
    Route::post('/polls/{slug}/toggle', [PollController::class, 'toggle'])->name('polls.toggle'); // Đóng/mở poll
    Route::get('/polls/{slug}/export.csv', [PollController::class, 'exportCsv'])->name('polls.export'); // Export CSV
    Route::delete('/polls/{slug}', [PollController::class, 'destroy'])->name('polls.destroy'); // Xóa poll
});

// ============================================
// AUTHENTICATION ROUTES
// ============================================

// Laravel Breeze routes (login, register, password reset, email verification)
require __DIR__.'/auth.php';

// Google OAuth routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('oauth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('oauth.google.callback');
Route::post('/auth/google/unlink', [GoogleAuthController::class, 'unlink'])->middleware('auth')->name('oauth.google.unlink');

// Local-only utility route: delete user by email (remove after use)
if (app()->environment('local')) {
    Route::get('/dev/delete-user', function(Request $request){
        $email = (string) $request->query('email', '');
        if ($email === '') {
            abort(400, 'Missing email');
        }
        $deleted = User::where('email', $email)->delete();
        return response("Deleted {$deleted} user(s) with email {$email}");
    })->name('dev.delete-user');
}
