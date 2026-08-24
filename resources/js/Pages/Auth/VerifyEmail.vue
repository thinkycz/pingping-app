<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps({ status: String });
const form = useForm({});
const sent = computed(() => props.status === 'verification-link-sent');
</script>

<template><GuestLayout><Head :title="$t('Verify email')" /><h1 class="text-2xl font-bold tracking-tight text-ink">{{ $t('Check your inbox') }}</h1><p class="mt-2 text-sm leading-6 text-muted">{{ $t('Open the verification link we sent before accessing your monitors. If it is missing, request another email below.') }}</p><p v-if="sent" role="status" class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-medium text-emerald-700">{{ $t('A new verification link has been sent.') }}</p><form class="mt-6" @submit.prevent="form.post(route('verification.send'))"><PrimaryButton class="w-full" :disabled="form.processing">{{ form.processing ? $t('Sending…') : $t('Resend verification email') }}</PrimaryButton></form><Link :href="route('logout')" method="post" as="button" class="mt-5 w-full rounded-lg py-2 text-center text-sm font-semibold text-muted hover:text-ink">{{ $t('Log out') }}</Link></GuestLayout></template>
