<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Use Bootstrap pagination to prevent SVG oversized buttons
        Paginator::useBootstrapFive();

        // Đăng ký Event Listener gửi email xác thực khi đăng ký trong Laravel 11
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Registered::class,
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        );

        // Tùy biến nội dung Email Xác thực tài khoản sang Tiếng Việt
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('36Shop - Xác thực địa chỉ email của bạn')
                ->greeting('Xin chào ' . ($notifiable->name ?? 'quý khách') . '!')
                ->line('Cảm ơn bạn đã đăng ký tài khoản tại 36Shop.')
                ->line('Vui lòng nhấn vào nút bên dưới để xác thực địa chỉ email và hoàn tất kích hoạt tài khoản:')
                ->action('Xác thực địa chỉ Email', $url)
                ->line('Liên kết xác thực này sẽ hết hạn sau 60 phút.')
                ->line('Nếu bạn không đăng ký tài khoản trên 36Shop, bạn hoàn toàn có thể bỏ qua thư này.')
                ->salutation('Trân trọng, Đội ngũ 36Shop');
        });

        View::composer(['layout.home_layout', 'layout.profile_layout', 'client.*'], function ($view) {
            try {
                if (Schema::hasTable('_category')) {
                    $categories = Cache::remember('global_categories_view', 3600, function () {
                        return Category::orderBy('id', 'desc')->get();
                    });
                    $view->with('categories', $categories);
                }
            } catch (\Throwable $e) {
                // Ignore during migrations or testing
            }
        });
    }
}
