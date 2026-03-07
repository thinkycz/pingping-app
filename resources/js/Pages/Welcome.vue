<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome" />

    <div class="bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 min-h-screen selection:bg-indigo-500 selection:text-white overflow-x-hidden">
        <!-- Navigation -->
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <ApplicationLogo class="h-10 w-10 text-indigo-600 dark:text-indigo-400" />
                <span class="text-xl font-bold tracking-tight">UptimePro</span>
            </div>

            <div v-if="canLogin" class="flex space-x-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md px-3 py-2 transition"
                >
                    Dashboard
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md px-3 py-2 transition"
                    >
                        Log in
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="font-semibold bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 rounded-md px-4 py-2 transition shadow-sm"
                    >
                        Register
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative isolate pt-14">
            <!-- Background Decoration -->
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>

            <div class="container mx-auto px-6 py-24 sm:py-32 lg:flex lg:items-center lg:gap-x-10 lg:py-40">
                <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-lg lg:flex-shrink-0">
                    <div class="mt-24 sm:mt-32 lg:mt-16">
                        <a href="#" class="inline-flex space-x-6">
                            <span class="rounded-full bg-indigo-600/10 px-3 py-1 text-sm font-semibold leading-6 text-indigo-600 dark:text-indigo-400 ring-1 ring-inset ring-indigo-600/10 dark:ring-indigo-400/20">What's new</span>
                            <span class="inline-flex items-center space-x-2 text-sm font-medium leading-6 text-gray-600 dark:text-gray-300">
                                <span>Just shipped v1.0</span>
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </a>
                    </div>
                    <h1 class="mt-10 text-4xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-gray-900 to-indigo-600 dark:from-white dark:to-indigo-400 sm:text-6xl drop-shadow-sm">
                        Never let your website go down silently
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                        Get instant alerts when your site crashes, monitor performance from multiple global locations, and keep your customers happy with robust uptime tracking.
                    </p>
                    <div class="mt-10 flex items-center gap-x-6">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition hover:-translate-y-0.5"
                        >
                            Get started for free
                        </Link>
                        <a href="#features" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            Learn more <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
                <div class="mx-auto mt-16 flex max-w-2xl sm:mt-24 lg:ml-10 lg:mr-0 lg:mt-0 lg:max-w-none lg:flex-none xl:ml-32">
                    <div class="max-w-3xl flex-none sm:max-w-5xl lg:max-w-none">
                        <div class="rounded-xl bg-white/5 p-2 ring-1 ring-inset ring-gray-900/10 dark:ring-white/10 lg:-m-4 lg:rounded-2xl lg:p-4 backdrop-blur-3xl shadow-2xl">
                            <div class="rounded-md bg-white dark:bg-gray-800 shadow-2xl ring-1 ring-gray-900/10 dark:ring-white/10 w-[76rem] overflow-hidden">
                                 <!-- Placeholder for an app screenshot/dashboard view -->
                                 <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/80 backdrop-blur-sm px-4 py-3 sm:px-6 flex items-center">
                                     <div class="flex space-x-2">
                                         <div class="w-3 h-3 rounded-full bg-red-400 shadow-sm"></div>
                                         <div class="w-3 h-3 rounded-full bg-amber-400 shadow-sm"></div>
                                         <div class="w-3 h-3 rounded-full bg-green-400 shadow-sm"></div>
                                     </div>
                                     <div class="ml-4 text-xs text-gray-500 dark:text-gray-400 font-mono bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-4 py-1 rounded-md shadow-inner">app.uptimepro.com/dashboard</div>
                                 </div>
                                 <div class="p-6 grid grid-cols-3 gap-6 bg-gray-50 dark:bg-gray-800/50">
                                     <div class="col-span-1 space-y-4">
                                         <div class="h-24 bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600 animate-pulse"></div>
                                         <div class="h-24 bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600 animate-pulse"></div>
                                         <div class="h-24 bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600 animate-pulse"></div>
                                     </div>
                                     <div class="col-span-2 space-y-4">
                                         <div class="h-40 bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600 animate-pulse relative overflow-hidden">
                                            <!-- fake chart line -->
                                            <div class="absolute inset-0 flex items-end">
                                                <svg class="w-full h-full text-indigo-400/30 dark:text-indigo-400/50" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0 100 V 50 Q 10 40, 20 60 T 40 40 T 60 70 T 80 30 T 100 50 V 100 Z" fill="currentColor"/>
                                                </svg>
                                            </div>
                                         </div>
                                         <div class="h-32 bg-white dark:bg-gray-700 rounded-xl shadow-sm border border-gray-100 dark:border-gray-600 animate-pulse"></div>
                                     </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Background Decoration -->
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features" class="py-24 sm:py-32 bg-white dark:bg-gray-800">
            <div class="container mx-auto px-6 max-w-7xl">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">Monitor Faster</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        Everything you need to keep your sites online
                    </p>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                        Powerful features designed to give you peace of mind and minimize downtime for your web applications.
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
                                <svg class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                </svg>
                                1-Minute Checks
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">We check your website every 60 seconds. Know exactly when your site goes down before your users do.</p>
                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
                                <svg class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M2 3a1 1 0 00-1 1v1a1 1 0 001 1h16a1 1 0 001-1V4a1 1 0 00-1-1H2z" />
                                    <path fill-rule="evenodd" d="M2 7.5h16l-.811 7.71a2 2 0 01-1.99 1.79H4.802a2 2 0 01-1.99-1.79L2 7.5zm5.22 1.72a.75.75 0 011.06 0L10 10.94l1.72-1.72a.75.75 0 111.06 1.06L11.06 12l1.72 1.72a.75.75 0 11-1.06 1.06L10 13.06l-1.72 1.72a.75.75 0 01-1.06-1.06L8.94 12l-1.72-1.72a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                </svg>
                                Instant Alerts
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">Get notified immediately via Email, SMS, Slack, or Webhook when an incident occurs.</p>
                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
                                <svg class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M2 4.75C2 3.784 2.784 3 3.75 3h12.5c.966 0 1.75.784 1.75 1.75v10.5A1.75 1.75 0 0116.25 17H3.75A1.75 1.75 0 012 15.25V4.75zm1.75-.25a.25.25 0 00-.25.25v10.5c0 .138.112.25.25.25h12.5a.25.25 0 00.25-.25V4.75a.25.25 0 00-.25-.25H3.75z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M12.5 7.25a.75.75 0 01.75-.75h2a.75.75 0 01.75.75v5a.75.75 0 01-.75.75h-2a.75.75 0 01-.75-.75v-5zM8.5 9.25a.75.75 0 01.75-.75h2a.75.75 0 01.75.75v3a.75.75 0 01-.75.75h-2a.75.75 0 01-.75-.75v-3zM4.5 11.25a.75.75 0 01.75-.75h2a.75.75 0 01.75.75v1a.75.75 0 01-.75.75h-2a.75.75 0 01-.75-.75v-1z" clip-rule="evenodd" />
                                </svg>
                                Detailed Analytics
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">Analyze response times and uptime percentages to ensure your service level agreements are met.</p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
            <div class="container mx-auto px-6 py-12 md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:order-2 space-x-6 text-gray-500 dark:text-gray-400">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </div>
                <div class="mt-8 md:order-1 md:mt-0 flex items-center justify-center md:justify-start space-x-3">
                    <ApplicationLogo class="h-6 w-6 text-gray-400" />
                    <p class="text-center text-xs leading-5 text-gray-500 dark:text-gray-400">
                        &copy; 2024 UptimePro, Inc. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
