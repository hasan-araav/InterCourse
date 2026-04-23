<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    Calendar,
    Users,
    Bell,
    TrendingUp,
    ShieldCheck,
    Zap,
    ArrowRight,
    Play
} from 'lucide-vue-next';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-white text-gray-900 font-sans selection:bg-indigo-100 selection:text-indigo-700">
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-2">
                        <ApplicationLogo class="h-8 w-8 text-indigo-600" />
                        <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-indigo-500 bg-clip-text text-transparent">
                            InterCourse
                        </span>
                    </div>

                    <div v-if="canLogin" class="flex items-center gap-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative overflow-hidden pt-16 pb-32">
            <div class="absolute inset-0 -z-10">
                <div class="absolute inset-y-0 right-1/2 -z-10 mr-16 w-[200%] origin-bottom-left skew-x-[-30deg] bg-white shadow-xl shadow-indigo-600/10 ring-1 ring-indigo-50 sm:mr-28 lg:mr-0 xl:mr-16 xl:origin-center"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
                    <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm font-medium mb-6">
                            <Zap class="w-4 h-4 fill-indigo-500" />
                            <span>New: Real-time Workshop Updates</span>
                        </div>
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block">Elevate Your Team's</span>
                            <span class="block text-indigo-600">Skills & Expertise</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                            The comprehensive workshop management platform designed for modern enterprises.
                            Schedule, register, and track professional development with ease.
                        </p>
                        <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <Link
                                    :href="route('login')"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all hover:scale-105 active:scale-95"
                                >
                                    Start Learning
                                    <ArrowRight class="ml-2 w-5 h-5" />
                                </Link>
                                <a
                                    href="#features"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-200 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all"
                                >
                                    <Play class="mr-2 w-4 h-4 fill-gray-400" />
                                    See How it Works
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
                        <div class="relative mx-auto w-full rounded-2xl shadow-2xl overflow-hidden border border-gray-100 bg-gray-50 aspect-video group">
                            <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500/10 to-emerald-500/10 mix-blend-overlay"></div>
                            <!-- Mock UI Representation -->
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="h-8 w-32 bg-indigo-200 rounded-md animate-pulse"></div>
                                    <div class="flex gap-2">
                                        <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
                                        <div class="h-8 w-8 bg-gray-200 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-3">
                                        <div class="h-24 bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                                            <div class="h-3 w-2/3 bg-gray-100 rounded mb-2"></div>
                                            <div class="h-2 w-1/2 bg-gray-50 rounded"></div>
                                        </div>
                                        <div class="h-24 bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                                            <div class="h-3 w-2/3 bg-gray-100 rounded mb-2"></div>
                                            <div class="h-2 w-1/2 bg-gray-50 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="h-full bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                                        <div class="h-4 w-4/5 bg-indigo-50 rounded mb-4"></div>
                                        <div class="space-y-2">
                                            <div class="h-2 w-full bg-gray-50 rounded"></div>
                                            <div class="h-2 w-full bg-gray-50 rounded"></div>
                                            <div class="h-2 w-3/4 bg-gray-50 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-indigo-600 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                    <div class="text-center">
                        <div class="text-3xl font-extrabold text-white">50+</div>
                        <div class="text-indigo-100 text-sm mt-1">Expert Speakers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-extrabold text-white">1.2k</div>
                        <div class="text-indigo-100 text-sm mt-1">Attendees</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-extrabold text-white">98%</div>
                        <div class="text-indigo-100 text-sm mt-1">Success Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-extrabold text-white">Real-time</div>
                        <div class="text-indigo-100 text-sm mt-1">Waitlisting</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-base font-semibold text-indigo-600 tracking-wide uppercase">Features</h2>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">Everything you need to grow</p>
                </div>

                <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 mb-6">
                            <Calendar class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Seamless Scheduling</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Organize and manage workshops with a powerful administrative suite. Track capacities and timing effortlessly.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 mb-6">
                            <Users class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Smart Waitlisting</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Automatic FIFO promotion logic ensures fair workshop access. Never worry about manual waitlist management.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 mb-6">
                            <Bell class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Real-time Notifications</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Stay updated with instant registration alerts and automated reminders before workshops start.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mb-6">
                            <TrendingUp class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Insights & Analytics</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Comprehensive dashboards for administrators to track workshop popularity and attendee engagement.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-6">
                            <ShieldCheck class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Role-Based Access</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Secure, controlled environments for both administrators and employees with granular permissions.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 mb-6">
                            <Zap class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Instant Feedback</h3>
                        <p class="text-gray-500 leading-relaxed">
                            WebSocket-powered UI updates ensure you always see the latest seat availability and status changes.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <div class="bg-white">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
                <div class="bg-indigo-700 rounded-3xl shadow-xl overflow-hidden lg:grid lg:grid-cols-2 lg:gap-4">
                    <div class="pt-10 pb-12 px-6 sm:pt-16 sm:px-16 lg:py-16 lg:pr-0 xl:py-20 xl:px-20">
                        <div class="lg:self-center">
                            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                                <span class="block">Ready to dive in?</span>
                                <span class="block">Join InterCourse today.</span>
                            </h2>
                            <p class="mt-4 text-lg leading-6 text-indigo-200">
                                Join hundreds of professionals already improving their skills through our platform.
                            </p>
                            <Link
                                :href="route('login')"
                                class="mt-8 bg-white border border-transparent rounded-xl shadow px-6 py-3 inline-flex items-center text-base font-medium text-indigo-600 hover:bg-indigo-50 transition-colors"
                            >
                                Get Started
                            </Link>
                        </div>
                    </div>
                    <div class="-mt-6 aspect-w-5 aspect-h-3 md:aspect-w-2 md:aspect-h-1">
                        <div class="bg-indigo-800/50 w-full h-full flex items-center justify-center p-12">
                            <div class="grid grid-cols-2 gap-4 w-full">
                                <div class="h-12 bg-white/10 rounded-lg animate-pulse"></div>
                                <div class="h-12 bg-white/10 rounded-lg animate-pulse" style="animation-delay: 150ms"></div>
                                <div class="h-12 bg-white/10 rounded-lg animate-pulse" style="animation-delay: 300ms"></div>
                                <div class="h-12 bg-white/10 rounded-lg animate-pulse" style="animation-delay: 450ms"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="flex items-center gap-2">
                        <ApplicationLogo class="h-6 w-6 text-indigo-600" />
                        <span class="text-lg font-bold text-gray-900">InterCourse</span>
                    </div>
                    <div class="flex gap-8">
                        <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">About</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">Features</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">Privacy</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">Terms</a>
                    </div>
                    <p class="text-sm text-gray-400">
                        &copy; 2026 InterCourse. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Any additional custom styles if needed */
</style>
