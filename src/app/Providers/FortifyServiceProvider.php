<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use App\Models\User; // Userモデルをインポート
use Illuminate\Support\Facades\Hash; // Hashファサードをインポート
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;

use Illuminate\Support\Facades\Validator;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse
        {
            public function toResponse($request)
            {
                return redirect('/login');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        // Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        // Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        // Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // RateLimiter::for('login', function (Request $request) {
        //     $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

        //     return Limit::perMinute(5)->by($throttleKey);
        // });

        // RateLimiter::for('two-factor', function (Request $request) {
        //     return Limit::perMinute(5)->by($request->session()->get('login.id'));
        // });

        // //会員登録 
        // Fortify::registerView(function () {
        //     return view('auth.register');
        // });
        // // ログイン画面
        // Fortify::loginView(function () {
        //     return view('auth.login');
        // });

        // ログインバリデーションのカスタマイズ

        Fortify::authenticateUsing(function (Request $request) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect('/login')
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
            // // 認証に失敗した場合
            // return redirect('/login')
            //     ->withErrors(['email' => 'メールアドレスまたはパスワードが一致していません'])
            //     ->withInput();
        });


        // RateLimiter::for('login', function (Request $request) {
        //     $email = (string) $request->email;

        //     return Limit::perMinute(10)->by($email . $request->ip());
        // });
    }
}
