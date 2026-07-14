<x-guest-layout>
    <x-slot name="title">Sign In</x-slot>

    <div class="mb-10 text-center">
        <h2 class="text-2xl font-black uppercase italic tracking-tight">Welcome Back</h2>
        <p class="text-xs font-medium text-zinc-400 mt-1">Enter your credentials to access the vault.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Email --}}
        <div>
            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2 block">Identity
                (Email)</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-5 py-4 bg-zinc-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-zinc-950 transition-all placeholder:text-zinc-300"
                placeholder="name@domain.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <div class="flex justify-between items-center mb-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-[9px] font-black uppercase tracking-widest text-zinc-300 hover:text-zinc-950 transition">Forgot?</a>
                @endif
            </div>
            <input type="password" name="password" required
                class="w-full px-5 py-4 bg-zinc-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-zinc-950 transition-all">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <label class="flex items-center gap-3 cursor-pointer group">
            <input type="checkbox" name="remember"
                class="w-4 h-4 rounded border-zinc-200 text-zinc-950 focus:ring-zinc-950">
            <span
                class="text-[10px] font-black uppercase tracking-widest text-zinc-400 group-hover:text-zinc-950 transition">Keep
                me signed in</span>
        </label>

        <button type="submit"
            class="w-full py-5 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-2xl shadow-xl hover:bg-zinc-800 transition-all active:scale-[0.98]">
            Authorize Access
        </button>

        <div class="relative py-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-zinc-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-zinc-400">Or continue with</span>
            </div>
        </div>

        <a href="{{ route('auth.google') }}"
            class="w-full py-3 px-4 border border-zinc-200 rounded-2xl text-sm font-bold text-zinc-700 hover:bg-zinc-50 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M12.24 10.285V13.4h6.887c-.275 1.565-1.88 4.604-6.887 4.604-4.33 0-7.866-3.577-7.866-8s3.536-8 7.866-8c2.46 0 4.105 1.025 5.047 1.926l2.427-2.334C17.955 2.192 15.34 1 12.24 1 6.033 1 1 6.033 1 12.24s5.033 11.24 11.24 11.24c6.478 0 10.793-4.537 10.793-11 0-.746-.08-1.32-.176-1.885H12.24z" />
            </svg>
            Google
        </a>

        <div class="pt-6 text-center border-t border-zinc-50">
            <p class="text-xs font-medium text-zinc-400">
                New to STS?
                <a href="{{ route('register') }}"
                    class="font-black text-zinc-950 uppercase tracking-tighter italic border-b border-zinc-950 ml-1">Create
                    Account</a>
            </p>
        </div>


    </form>
</x-guest-layout>
