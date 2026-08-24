<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ canResetPassword: Boolean, status: String });
const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), { onFinish: () => form.reset('password') });
</script>

<template><GuestLayout><Head :title="$t('Log in')" /><h1 class="text-2xl font-bold tracking-tight text-ink">{{ $t('Welcome back') }}</h1><p class="mt-2 text-sm leading-6 text-muted">{{ $t('Log in to check your websites and recent alerts.') }}</p><p v-if="status" role="status" class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm font-medium text-emerald-700">{{ status }}</p><form class="mt-6 space-y-5" @submit.prevent="submit"><div><InputLabel for="email" :value="$t('Email address')" /><TextInput id="email" v-model="form.email" type="email" class="mt-2" autocomplete="username" autofocus required :aria-invalid="Boolean(form.errors.email)" /><InputError :message="form.errors.email" /></div><div><div class="flex items-center justify-between"><InputLabel for="password" :value="$t('Password')" /><Link v-if="canResetPassword" :href="route('password.request')" class="rounded text-xs font-semibold text-primary-700 hover:text-primary-950">{{ $t('Forgot password?') }}</Link></div><TextInput id="password" v-model="form.password" type="password" class="mt-2" autocomplete="current-password" required :aria-invalid="Boolean(form.errors.password)" /><InputError :message="form.errors.password" /></div><label class="flex items-center gap-2 text-sm text-muted"><Checkbox v-model:checked="form.remember" name="remember" />{{ $t('Remember me') }}</label><PrimaryButton class="w-full" :disabled="form.processing">{{ form.processing ? $t('Logging in…') : $t('Log in') }}</PrimaryButton></form><p class="mt-6 text-center text-sm text-muted">{{ $t('New to PingPing?') }} <Link :href="route('register')" class="font-semibold text-primary-700 hover:text-primary-950">{{ $t('Create an account') }}</Link></p></GuestLayout></template>
