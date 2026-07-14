<x-guest-layout>
    <x-slot name="title">Register</x-slot>

    <div class="mb-10 text-center">
        <h2 class="text-2xl font-black uppercase italic tracking-tight">Join the Club</h2>
        <p class="text-xs font-medium text-zinc-400 mt-1">Create your STS profile to start your archive.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Name --}}
        <div>
            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2 block">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full px-5 py-4 bg-zinc-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-zinc-950 transition-all"
                placeholder="Enter your name">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div>
            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2 block">Email
                Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full px-5 py-4 bg-zinc-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-zinc-950 transition-all"
                placeholder="name@domain.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2 block">Security
                    Key</label>
                <input type="password" name="password" required
                    class="w-full px-5 py-4 bg-zinc-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-zinc-950 transition-all">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2 block">Confirm
                    Key</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-5 py-4 bg-zinc-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-zinc-950 transition-all">
            </div>
            <x-input-error :messages="$errors->get('password')" class="col-span-2 mt-1" />
        </div>

        <button type="submit"
            class="w-full py-5 bg-zinc-950 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-2xl shadow-xl hover:bg-zinc-800 transition-all mt-4">
            Create Profile
        </button>

        <div class="pt-6 text-center border-t border-zinc-50">
            <p class="text-xs font-medium text-zinc-400">
                Already have an account?
                <a href="{{ route('login') }}"
                    class="font-black text-zinc-950 uppercase tracking-tighter italic border-b border-zinc-950 ml-1">Sign
                    In</a>
            </p>
        </div>
    </form>
</x-guest-layout>
