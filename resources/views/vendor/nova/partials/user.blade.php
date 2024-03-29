<dropdown-trigger class="h-9 flex items-center p-relative">

    @isset($user->email)
        <img
            src="https://secure.gravatar.com/avatar/{{ md5(\Illuminate\Support\Str::lower($user->email)) }}?size=512"
            class="rounded-full w-8 h-8 mr-3"
        />
    @endisset

    <span class="text-90">
        {{ $user->information->fullName ?? $user->name ?? $user->email }}
    </span>
</dropdown-trigger>

<dropdown-menu slot="menu" width="200" direction="rtl">
    <ul class="list-reset">
        <li>
            <a href="#" class="block no-underline text-90 hover:bg-30 p-3">
                {{ auth()->user()->email }}
            </a>
        </li>
        <li>
            <a href="/resources/users/{{ auth()->user()->id }}" class="block no-underline text-90 hover:bg-30 p-3">
                {{ __('My Profile') }}
            </a>
        </li>
        <li>
            <a href="/resources/users/{{ auth()->user()->id }}/edit" class="block no-underline text-90 hover:bg-30 p-3">
                {{ __('Change Password') }}
            </a>
        </li>
        <li>
            <a href="{{ route('nova.logout') }}" class="block no-underline text-90 hover:bg-30 p-3">
                {{ __('Logout') }}
            </a>
        </li>
    </ul>
</dropdown-menu>
