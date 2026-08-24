<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, ShieldCheckIcon } from '@heroicons/vue/20/solid';

const form = useForm({ url: '', alias: '', interval: 5 });
const submit = () => form.post(route('monitors.store'));
</script>

<template>
    <Head :title="$t('New monitor')" />
    <AuthenticatedLayout>
        <div class="page-shell py-8 sm:py-10">
            <Link :href="route('dashboard')" class="mb-5 inline-flex items-center gap-1.5 rounded-lg text-sm font-semibold text-muted hover:text-ink"><ArrowLeftIcon class="h-4 w-4" />{{ $t('Back to dashboard') }}</Link>
            <PageHeader :title="$t('Add a website')" :description="$t('The first check starts after the monitor is created. Results appear on its detail page.')" />

            <div class="mt-7 grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(260px,1fr)]">
                <form class="surface p-5 sm:p-7" novalidate @submit.prevent="submit">
                    <div>
                        <InputLabel for="url" :value="$t('Public website URL')" />
                        <TextInput id="url" v-model="form.url" dusk="monitor-url" type="url" class="mt-2" placeholder="https://example.com" autocomplete="url" autofocus required :aria-invalid="Boolean(form.errors.url)" :aria-describedby="form.errors.url ? 'url-error' : 'url-help'" />
                        <p id="url-help" class="mt-1.5 text-xs leading-5 text-muted">{{ $t('Use HTTP or HTTPS on port 80 or 443. Private and local addresses are blocked.') }}</p>
                        <InputError id="url-error" :message="form.errors.url" />
                    </div>

                    <div class="mt-6">
                        <InputLabel for="alias" :value="$t('Friendly name')" />
                        <TextInput id="alias" v-model="form.alias" dusk="monitor-alias" type="text" class="mt-2" :placeholder="$t('Storefront (optional)')" autocomplete="off" :aria-invalid="Boolean(form.errors.alias)" />
                        <InputError :message="form.errors.alias" />
                    </div>

                    <div class="mt-6">
                        <InputLabel for="interval" :value="$t('Check interval')" />
                        <select id="interval" v-model="form.interval" class="mt-2 min-h-11 w-full rounded-xl border-line bg-white text-sm text-ink focus:border-primary-500 focus:ring-primary-500" :aria-invalid="Boolean(form.errors.interval)">
                            <option v-for="minutes in [5, 15, 30, 60]" :key="minutes" :value="minutes">{{ $t(':minutes minutes', { minutes }) }}</option>
                        </select>
                        <InputError :message="form.errors.interval" />
                    </div>

                    <div class="mt-7 flex items-center justify-end gap-3 border-t border-line pt-5">
                        <Link :href="route('dashboard')" class="inline-flex min-h-10 items-center rounded-xl px-4 text-sm font-semibold text-muted hover:bg-gray-50 hover:text-ink">{{ $t('Cancel') }}</Link>
                        <PrimaryButton dusk="create-monitor" :disabled="form.processing">{{ form.processing ? $t('Creating…') : $t('Create monitor') }}</PrimaryButton>
                    </div>
                </form>

                <aside class="h-fit rounded-2xl border border-primary-100 bg-primary-50 p-5">
                    <div class="grid h-10 w-10 place-items-center rounded-xl bg-white text-primary-700"><ShieldCheckIcon class="h-5 w-5" /></div>
                    <h2 class="mt-4 font-semibold text-ink">{{ $t('Safe by default') }}</h2>
                    <p class="mt-2 text-sm leading-6 text-muted">{{ $t('PingPing validates the destination and every redirect before connecting. HTTPS checks verify certificate trust and hostname.') }}</p>
                </aside>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
