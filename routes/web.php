<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ImageUploadController;
use App\Models\Poll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return view('welcome');
});

// Switch locale
Route::get('/locale/{lang}', function($lang){
    if (!in_array($lang, ['vi','en'])) { $lang = 'vi'; }
    Session::put('locale', $lang);
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

Route::middleware('auth')->group(function () {
    Route::post('/profile/locale', [ProfileController::class, 'updateLocale'])->name('profile.locale');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Poll routes
Route::middleware('auth')->group(function () {
    Route::get('/polls/create', [PollController::class, 'create'])->name('polls.create');
    Route::post('/polls', [PollController::class, 'store'])->name('polls.store');
});

// Media upload routes (bypass CSRF for API calls)
Route::prefix('api')->group(function () {
    Route::post('/media/upload', [ImageUploadController::class, 'upload'])->name('media.upload');
    Route::post('/media/validate-url', [ImageUploadController::class, 'validateUrl'])->name('media.validate-url');
    Route::delete('/media/delete', [ImageUploadController::class, 'delete'])->name('media.delete');
});
// Quick Access Route
Route::get('/quick-access/{code}', [PollController::class, 'quickAccess'])->name('polls.quick-access');
Route::get('/polls/{slug}/access', [PollController::class, 'accessForm'])->name('polls.access');
Route::post('/polls/{slug}/access', [PollController::class, 'accessCheck'])->name('polls.access.check');
Route::middleware(\App\Http\Middleware\EnsurePollAccess::class)->group(function () {
    // Guest name capture before voting
    Route::get('/polls/{slug}/name', [\App\Http\Controllers\PollController::class, 'nameForm'])->name('polls.name');
    Route::post('/polls/{slug}/name', [\App\Http\Controllers\PollController::class, 'saveName'])->name('polls.name.save');
    Route::get('/polls/{slug}', [PollController::class, 'vote'])->name('polls.vote');
    Route::get('/polls/{slug}/results', [PollController::class, 'show'])->name('polls.show');
    Route::post('/polls/{slug}/vote', [VoteController::class, 'store'])->name('polls.vote.store');
    Route::post('/polls/{slug}/comments', [PollController::class, 'comment'])->name('polls.comment');
});
Route::middleware('auth')->group(function () {
    Route::post('/polls/{slug}/toggle', [PollController::class, 'toggle'])->name('polls.toggle');
    Route::get('/polls/{slug}/export.csv', [PollController::class, 'exportCsv'])->name('polls.export');
    Route::delete('/polls/{slug}', [PollController::class, 'destroy'])->name('polls.destroy');
});

require __DIR__.'/auth.php';

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('oauth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('oauth.google.callback');
Route::post('/auth/google/unlink', [GoogleAuthController::class, 'unlink'])->middleware('auth')->name('oauth.google.unlink');
