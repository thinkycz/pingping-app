<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();

const currentLocale = computed(() => page.props.locale || 'cs');

const switchLanguage = (lang) => {
    router.post(route('language.store'), { language: lang }, {
        onSuccess: () => {
            window.location.reload();
        }
    });
};
</script>

<template>
    <div class="hidden sm:flex sm:items-center sm:ms-6">
        <Dropdown align="right" width="48">
            <template #trigger>
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        <span v-if="currentLocale === 'en'" class="text-xl">🇬🇧</span>
                        <span v-else class="text-xl">🇨🇿</span>

                        <svg
                            class="-me-0.5 ms-2 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </span>
            </template>

            <template #content>
                <button
                    @click="switchLanguage('en')"
                    class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800"
                >
                    <span class="mr-2">🇬🇧</span> English
                </button>
                <button
                    @click="switchLanguage('cs')"
                    class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800"
                >
                    <span class="mr-2">🇨🇿</span> Čeština
                </button>
            </template>
        </Dropdown>
    </div>
</template>
