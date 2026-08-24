<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

const switchLanguage = (language) => {
    if (language === currentLocale.value) return;
    router.post(route('language.store'), { language }, {
        preserveScroll: true,
        onSuccess: () => loadLanguageAsync(language),
    });
};
</script>

<template>
    <div class="inline-flex rounded-xl border border-line bg-white p-1" :aria-label="$t('Language')">
        <button
            v-for="language in ['en', 'cs']"
            :key="language"
            type="button"
            class="min-h-8 rounded-lg px-2.5 text-xs font-semibold uppercase transition"
            :class="currentLocale === language ? 'bg-primary-50 text-primary-700' : 'text-muted hover:bg-gray-50 hover:text-ink'"
            :aria-pressed="currentLocale === language"
            :aria-label="language === 'en' ? $t('Switch to English') : $t('Switch to Czech')"
            :dusk="`language-${language}`"
            @click="switchLanguage(language)"
        >
            {{ language }}
        </button>
    </div>
</template>
