<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
const props = defineProps({ email: { type: String, required: true }, token: { type: String, required: true } });
const form = useForm({ token: props.token, email: props.email, password: '', password_confirmation: '' });
const submit = () => form.post(route('password.store'), { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template><GuestLayout><Head :title="$t('Choose new password')" /><h1 class="text-2xl font-bold tracking-tight text-ink">{{ $t('Choose a new password') }}</h1><p class="mt-2 text-sm leading-6 text-muted">{{ $t('Use a long, unique password for your PingPing account.') }}</p><form class="mt-6 space-y-5" @submit.prevent="submit"><div><InputLabel for="email" :value="$t('Email address')" /><TextInput id="email" v-model="form.email" type="email" class="mt-2" autocomplete="username" required :aria-invalid="Boolean(form.errors.email)" /><InputError :message="form.errors.email" /></div><div><InputLabel for="password" :value="$t('New password')" /><TextInput id="password" v-model="form.password" type="password" class="mt-2" autocomplete="new-password" autofocus required :aria-invalid="Boolean(form.errors.password)" /><InputError :message="form.errors.password" /></div><div><InputLabel for="password_confirmation" :value="$t('Confirm password')" /><TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-2" autocomplete="new-password" required /></div><PrimaryButton class="w-full" :disabled="form.processing">{{ form.processing ? $t('Saving…') : $t('Reset password') }}</PrimaryButton></form></GuestLayout></template>
