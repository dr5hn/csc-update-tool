<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('change-requests.index') }}" class="flex items-center">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        <span class="text-2xl font-bold ml-2">
                            {{ config('app.name') }}
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('change-requests.index')" :active="request()->routeIs('change-requests.index')">
                        {{ __('Change Requests') }}
                    </x-nav-link>
                    <x-nav-link :href="'https://demo.countrystatecity.in/'" :target="'_blank'" class="flex items-center">
                        <span>{{ __('Data Explorer') }}</span>
                        <svg class="w-[20px] ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="none" stroke="currentColor"><path d="M388.364 242.764V421.455C388.364 432.738 383.881 443.56 375.902 451.539C367.924 459.518 357.102 464 345.818 464H90.5455C79.2617 464 68.4401 459.518 60.4613 451.539C52.4825 443.56 48 432.738 48 421.455V166.182C48 154.898 52.4825 144.076 60.4613 136.098C68.4401 128.119 79.2617 123.636 90.5455 123.636H269.236" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"></path><path d="M464 180.364L464 48L331.636 48" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"></path><path d="M216 296L464 48" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    </x-nav-link>
                    <x-nav-link :href="'https://countrystatecity.in/'" :target="'_blank'" class="flex items-center">
                        <span>{{ __('API') }}</span>
                        <svg class="w-[20px] ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="none" stroke="currentColor"><path d="M388.364 242.764V421.455C388.364 432.738 383.881 443.56 375.902 451.539C367.924 459.518 357.102 464 345.818 464H90.5455C79.2617 464 68.4401 459.518 60.4613 451.539C52.4825 443.56 48 432.738 48 421.455V166.182C48 154.898 52.4825 144.076 60.4613 136.098C68.4401 128.119 79.2617 123.636 90.5455 123.636H269.236" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"></path><path d="M464 180.364L464 48L331.636 48" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"></path><path d="M216 296L464 48" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    </x-nav-link>
                    <x-nav-link :href="'https://github.com/dr5hn/countries-states-cities-database'" :target="'_blank'" class="flex items-center">
                        <span>{{ __('GitHub') }}</span>
                        <svg class="w-[20px] ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="none" stroke="currentColor"><path d="M388.364 242.764V421.455C388.364 432.738 383.881 443.56 375.902 451.539C367.924 459.518 357.102 464 345.818 464H90.5455C79.2617 464 68.4401 459.518 60.4613 451.539C52.4825 443.56 48 432.738 48 421.455V166.182C48 154.898 52.4825 144.076 60.4613 136.098C68.4401 128.119 79.2617 123.636 90.5455 123.636H269.236" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"></path><path d="M464 180.364L464 48L331.636 48" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"></path><path d="M216 296L464 48" stroke="currentColor" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-base leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('change-requests.index')" :active="request()->routeIs('change-requests.index')">
                {{ __('Change Requests') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
