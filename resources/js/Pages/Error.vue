<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    AlertCircle,
    Search,
    ShieldAlert,
    ServerCrash,
    ArrowLeft,
    Home,
    LifeBuoy
} from 'lucide-vue-next';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({
    status: Number,
});

const goBack = () => {
    window.history.back();
};

const errorDetails = computed(() => {
    return {
        503: {
            title: 'Service Unavailable',
            description: 'We are currently performing some maintenance. We will be back shortly!',
            icon: LifeBuoy,
            color: 'text-amber-600',
            bgColor: 'bg-amber-50',
        },
        500: {
            title: 'Server Error',
            description: 'Whoops, something went wrong on our servers. Our engineers are on it.',
            icon: ServerCrash,
            color: 'text-rose-600',
            bgColor: 'bg-rose-50',
        },
        404: {
            title: 'Page Not Found',
            description: 'Sorry, the page you are looking for could not be found or has been moved.',
            icon: Search,
            color: 'text-indigo-600',
            bgColor: 'bg-indigo-50',
        },
        403: {
            title: 'Access Forbidden',
            description: 'Sorry, you do not have permission to access this page or resource.',
            icon: ShieldAlert,
            color: 'text-orange-600',
            bgColor: 'bg-orange-50',
        },
    }[props.status] || {
        title: 'An Error Occurred',
        description: 'An unexpected error has occurred. Please try again later.',
        icon: AlertCircle,
        color: 'text-gray-600',
        bgColor: 'bg-gray-50',
    };
});
</script>

<template>
    <Head :title="errorDetails.title" />

    <div class="min-h-screen bg-white flex flex-col items-center justify-center p-6 font-sans selection:bg-indigo-100 selection:text-indigo-700">
        <!-- Background Decoration -->
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-indigo-50 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-emerald-50 rounded-full blur-3xl opacity-50"></div>
        </div>

        <div class="max-w-md w-full text-center">
            <!-- Logo -->
            <Link href="/" class="inline-flex items-center gap-2 mb-12">
                <ApplicationLogo class="h-10 w-10 text-indigo-600" />
                <span class="text-2xl font-bold text-gray-900 tracking-tight">InterCourse</span>
            </Link>

            <!-- Error Icon -->
            <div :class="[errorDetails.bgColor, 'w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-8 transition-transform hover:scale-110 duration-300 shadow-sm']">
                <component :is="errorDetails.icon" :class="[errorDetails.color, 'w-12 h-12']" />
            </div>

            <!-- Error Content -->
            <div class="space-y-4 mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ status }}: {{ errorDetails.title }}
                </h1>
                <p class="text-lg text-gray-500 leading-relaxed">
                    {{ errorDetails.description }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button
                    @click="goBack"
                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-200 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all active:scale-95"
                >
                    <ArrowLeft class="mr-2 w-4 h-4" />
                    Go Back
                </button>
                <Link
                    href="/"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all active:scale-95"
                >
                    <Home class="mr-2 w-4 h-4" />
                    Back to Home
                </Link>
            </div>
        </div>

        <!-- Support Info -->
        <div class="mt-24 text-center">
            <p class="text-sm text-gray-400">
                Need help? <a href="#" class="text-indigo-600 font-medium hover:underline">Contact our support team</a>
            </p>
        </div>

        <footer class="mt-auto py-8 text-center text-xs text-gray-400 uppercase tracking-widest">
            &copy; 2026 InterCourse &bull; Error Management System
        </footer>
    </div>
</template>
