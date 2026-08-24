<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });
const submit = () => form.post(route('register'), { onFinish: () => form.reset('password', 'password_confirmation') });
</script>

<template><GuestLayout><Head :title="$t('Create account')" /><h1 class="text-2xl font-bold tracking-tight text-ink">{{ $t('Create your account') }}</h1><p class="mt-2 text-sm leading-6 text-muted">{{ $t('Verify your email, then add your first public website.') }}</p><form class="mt-6 space-y-5" @submit.prevent="submit"><div><InputLabel for="name" :value="$t('Name')" /><TextInput id="name" v-model="form.name" class="mt-2" autocomplete="name" autofocus required :aria-invalid="Boolean(form.errors.name)" /><InputError :message="form.errors.name" /></div><div><InputLabel for="email" :value="$t('Email address')" /><TextInput id="email" v-model="form.email" type="email" class="mt-2" autocomplete="username" required :aria-invalid="Boolean(form.errors.email)" /><InputError :message="form.errors.email" /></div><div><InputLabel for="password" :value="$t('Password')" /><TextInput id="password" v-model="form.password" type="password" class="mt-2" autocomplete="new-password" required :aria-invalid="Boolean(form.errors.password)" /><InputError :message="form.errors.password" /></div><div><InputLabel for="password_confirmation" :value="$t('Confirm password')" /><TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-2" autocomplete="new-password" required /></div><PrimaryButton class="w-full" :disabled="form.processing">{{ form.processing ? $t('Creating account…') : $t('Create account') }}</PrimaryButton></form><p class="mt-6 text-center text-sm text-muted">{{ $t('Already have an account?') }} <Link :href="route('login')" class="font-semibold text-primary-700 hover:text-primary-950">{{ $t('Log in') }}</Link></p></GuestLayout></template>
