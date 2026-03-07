<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { 
  Bars3BottomLeftIcon, 
  BellIcon, 
  UsersIcon, 
  Cog6ToothIcon, 
  CreditCardIcon, 
  Bars3Icon, 
  XMarkIcon 
} from '@heroicons/vue/20/solid';

const showingNavigationDropdown = ref(false);

const navigation = [
  { name: 'Monitoring', href: route('dashboard'), current: route().current('dashboard'), icon: Bars3BottomLeftIcon },
  { name: 'Notifications', href: '#', current: false, icon: BellIcon },
  { name: 'Team', href: '#', current: false, icon: UsersIcon },
  { name: 'Settings', href: '#', current: false, icon: Cog6ToothIcon },
  { name: 'Billing', href: '#', current: false, icon: CreditCardIcon },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50 font-sans">
        <nav class="bg-[#2D6BBB]">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center space-x-8">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('dashboard')" class="text-white">
                                <!-- Temporary Logo mimicking the image -->
                                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 8v8 M8 12h8"></path>
                                </svg>
                            </Link>
                        </div>

                        <!-- Desktop Nav -->
                        <div class="hidden sm:block">
                            <div class="flex items-baseline space-x-1">
                                <Link 
                                    v-for="item in navigation" 
                                    :key="item.name" 
                                    :href="item.href" 
                                    :class="[item.current ? 'bg-[#245696] text-white' : 'text-blue-100 hover:bg-[#245696] hover:text-white', 'flex items-center rounded-md px-4 py-2 text-sm font-medium transition duration-150']"
                                    :aria-current="item.current ? 'page' : undefined"
                                >
                                    <component :is="item.icon" class="mr-2 h-4 w-4" aria-hidden="true" />
                                    {{ item.name }}
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex items-center">
                            <!-- Profile dropdown -->
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button type="button" class="flex max-w-xs items-center rounded-full bg-blue-600 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
                                        <span class="sr-only">Open user menu</span>
                                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name=User&background=random" alt="" />
                                    </button>
                                </template>

                                <template #content>
                                    <div class="px-4 py-2 border-b text-xs text-gray-500">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Log out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" type="button" class="relative inline-flex items-center justify-center rounded-md bg-[#245696] p-2 text-blue-200 hover:bg-[#245696] hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
                            <span class="absolute -inset-0.5" />
                            <span class="sr-only">Open main menu</span>
                            <Bars3Icon v-if="!showingNavigationDropdown" class="block h-6 w-6" aria-hidden="true" />
                            <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-show="showingNavigationDropdown" class="sm:hidden">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <Link 
                        v-for="item in navigation" 
                        :key="item.name" 
                        :href="item.href" 
                        :class="[item.current ? 'bg-[#245696] text-white' : 'text-blue-100 hover:bg-[#245696] hover:text-white', 'flex items-center rounded-md px-3 py-2 text-base font-medium']"
                        :aria-current="item.current ? 'page' : undefined"
                    >
                        <component :is="item.icon" class="mr-3 h-5 w-5" aria-hidden="true" />
                        {{ item.name }}
                    </Link>
                </div>
                <div class="border-t border-[#3173C5] pb-3 pt-4">
                    <div class="flex items-center px-5">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=User&background=random" alt="" />
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-white">{{ $page.props.auth.user.name }}</div>
                            <div class="text-sm font-medium text-blue-200">{{ $page.props.auth.user.email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1 px-2">
                        <Link :href="route('profile.edit')" class="block rounded-md px-3 py-2 text-base font-medium text-blue-100 hover:bg-[#245696] hover:text-white">Profile</Link>
                        <Link :href="route('logout')" method="post" as="button" class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-blue-100 hover:bg-[#245696] hover:text-white">Log out</Link>
                    </div>
                </div>
            </div>
        </nav>

        <header v-if="$slots.header" class="bg-white shadow-sm border-b border-gray-200">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
