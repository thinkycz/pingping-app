<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
const form = useForm({ password: '' });
const submit = () => form.post(route('password.confirm'), { onFinish: () => form.reset() });
</script>

<template><GuestLayout><Head :title="$t('Confirm password')" /><h1 class="text-2xl font-bold tracking-tight text-ink">{{ $t('Confirm it is you') }}</h1><p class="mt-2 text-sm leading-6 text-muted">{{ $t('This action is sensitive. Enter your password to continue.') }}</p><form class="mt-6 space-y-5" @submit.prevent="submit"><div><InputLabel for="password" :value="$t('Password')" /><TextInput id="password" v-model="form.password" type="password" class="mt-2" autocomplete="current-password" autofocus required :aria-invalid="Boolean(form.errors.password)" /><InputError :message="form.errors.password" /></div><PrimaryButton class="w-full" :disabled="form.processing">{{ $t('Confirm password') }}</PrimaryButton></form></GuestLayout></template>
