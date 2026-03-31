<x-guest-layout>
<div class="min-h-screen flex">

    {{-- ── Left brand panel ───────────────────────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-900 via-indigo-800 to-indigo-700 flex-col justify-between p-12">
        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center font-bold text-white text-base">
                PT
            </div>
            <span class="text-white text-xl font-semibold tracking-tight">Project Tracker</span>
        </div>

        {{-- Headline --}}
        <div>
            <h1 class="text-4xl font-bold text-white leading-tight mb-4">
                Join your team.<br>Start delivering.
            </h1>
            <p class="text-indigo-200 text-lg leading-relaxed mb-10">
                Create your account and get immediate access to your team's tasks and project timelines.
            </p>

            {{-- Role explanations --}}
            <div class="space-y-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">Member</p>
                        <p class="text-indigo-300 text-sm">View and update your assigned tasks. Track pending, in-progress and completed work.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">Team Lead</p>
                        <p class="text-indigo-300 text-sm">Assign tasks to your team members and monitor progress via timeline and summary views.</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-indigo-400 text-xs">&copy; {{ date('Y') }} Project Tracker. All rights reserved.</p>
    </div>

    {{-- ── Right form panel ────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col justify-center items-center px-6 sm:px-12 lg:px-16 py-12">
        {{-- Mobile logo --}}
        <div class="flex items-center gap-2 mb-8 lg:hidden">
            <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center font-bold text-white text-sm">PT</div>
            <span class="text-gray-900 text-lg font-semibold">Project Tracker</span>
        </div>

        <div class="w-full max-w-md">
            <div class="mb-7">
                <h2 class="text-3xl font-bold text-gray-900">Create an account</h2>
                <p class="text-gray-500 mt-1 text-sm">Fill in the details below to get started.</p>
            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Full name</label>
                    <input id="name"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           autocomplete="name"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('name') border-red-400 @enderror"
                           placeholder="Juan dela Cruz">
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autocomplete="username"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('email') border-red-400 @enderror"
                           placeholder="you@example.com">
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">I am a…</label>
                    <select id="role"
                            name="role"
                            required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('role') border-red-400 @enderror">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>— Select your role —</option>
                        <option value="member"    {{ old('role') === 'member'    ? 'selected' : '' }}>Member</option>
                        <option value="team_lead" {{ old('role') === 'team_lead' ? 'selected' : '' }}>Team Lead</option>
                    </select>
                    <p class="mt-1.5 text-xs text-gray-400">Admin and client accounts are created by an administrator.</p>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="new-password"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition @error('password') border-red-400 @enderror"
                           placeholder="Min. 8 characters">
                </div>

                {{-- Confirm password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Confirm password
                    </label>
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                           placeholder="••••••••">
                </div>

                {{-- Terms (only if Jetstream feature is enabled) --}}
                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="flex items-start gap-2 pt-1">
                        <input id="terms"
                               type="checkbox"
                               name="terms"
                               required
                               class="mt-0.5 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="terms" class="text-xs text-gray-500 leading-relaxed">
                            I agree to the
                            <a href="{{ route('terms.show') }}" target="_blank" class="text-indigo-600 hover:underline">Terms of Service</a>
                            and
                            <a href="{{ route('policy.show') }}" target="_blank" class="text-indigo-600 hover:underline">Privacy Policy</a>.
                        </label>
                    </div>
                @endif

                {{-- Submit --}}
                <div class="pt-1">
                    <button type="submit"
                            class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Create account
                    </button>
                </div>
            </form>

            {{-- Login link --}}
            <p class="mt-6 text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium transition">
                    Sign in
                </a>
            </p>
        </div>
    </div>

</div>
</x-guest-layout>
