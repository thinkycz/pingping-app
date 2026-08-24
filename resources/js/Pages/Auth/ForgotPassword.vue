<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
defineProps({ status: String });
const form = useForm({ email: '' });
</script>

<template><GuestLayout><Head :title="$t('Reset password')" /><h1 class="text-2xl font-bold tracking-tight text-ink">{{ $t('Reset your password') }}</h1><p class="mt-2 text-sm leading-6 text-muted">{{ $t('Enter your account email and we will send a secure reset link.') }}</p><p v-if="status" role="status" class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-medium text-emerald-700">{{ status }}</p><form class="mt-6 space-y-5" @submit.prevent="form.post(route('password.email'))"><div><InputLabel for="email" :value="$t('Email address')" /><TextInput id="email" v-model="form.email" type="email" class="mt-2" autocomplete="username" autofocus required :aria-invalid="Boolean(form.errors.email)" /><InputError :message="form.errors.email" /></div><PrimaryButton class="w-full" :disabled="form.processing">{{ form.processing ? $t('Sending…') : $t('Send reset link') }}</PrimaryButton></form><p class="mt-6 text-center text-sm"><Link :href="route('login')" class="font-semibold text-primary-700 hover:text-primary-950">{{ $t('Back to log in') }}</Link></p></GuestLayout></template>
