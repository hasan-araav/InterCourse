<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import {
    ChevronLeft,
    Calendar,
    Clock,
    Users,
    UserCheck,
    AlertCircle,
    Info,
    CheckCircle2,
    Activity,
    ArrowRight,
    MapPin,
    ShieldCheck,
    Mail
} from 'lucide-vue-next';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';

const props = defineProps({
    workshop: Object,
});

const workshopData = ref({ ...props.workshop });
const page = usePage();
let echoChannels = [];

// Watch for prop changes and update local state
watch(() => props.workshop, (newWorkshop) => {
    workshopData.value = { ...newWorkshop };
}, { deep: true });

onMounted(() => {
    if (window.Echo) {
        // Public channel for workshop stats
        window.Echo.channel('workshops.' + workshopData.value.id)
            .listen('.registration.updated', (data) => {
                workshopData.value.remaining_seats = data.capacity - data.confirmed_count;
                workshopData.value.confirmed_count = data.confirmed_count;
            });
        echoChannels.push('workshops.' + workshopData.value.id);

        // Private channel for personal registration status updates
        window.Echo.private('App.Models.User.' + page.props.auth.user.id)
            .listen('.user.registration.updated', (data) => {
                if (workshopData.value.id === data.workshop_id && workshopData.value.user_registration) {
                    workshopData.value.user_registration.status = data.status;
                    workshopData.value.user_registration.position = data.position;
                }
            });
        echoChannels.push('App.Models.User.' + page.props.auth.user.id);
    }
});

onUnmounted(() => {
    echoChannels.forEach((channelName) => window.Echo.leave(channelName));
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString([], {
        weekday: 'long',
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const register = () => {
    router.post(route('workshops.register', workshopData.value.id));
};

const cancel = () => {
    if (confirm('Are you sure you want to cancel your registration?')) {
        router.delete(route('workshops.cancel', workshopData.value.id));
    }
};
</script>

<template>
    <Head :title="workshopData.title" />

    <EmployeeLayout>
        <template #header>
            <div class="flex items-center justify-between py-4">
                <Link :href="route('dashboard')" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors group">
                    <ChevronLeft class="h-4 w-4 mr-1 group-hover:-translate-x-1 transition-transform" />
                    Back to Hub
                </Link>
                <div v-if="workshopData.user_registration" class="flex items-center space-x-2">
                    <span
                        :class="[
                            workshopData.user_registration.status === 'confirmed' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100',
                            'px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full border shadow-sm'
                        ]"
                    >
                        {{ workshopData.user_registration.status === 'confirmed' ? 'Confirmed' : 'Waitlisted #' + workshopData.user_registration.position }}
                    </span>
                </div>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.message" class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center text-emerald-700 font-bold text-sm animate-in fade-in slide-in-from-top-4 duration-500">
                <div class="bg-emerald-100 p-1 rounded-lg mr-3">
                    <CheckCircle2 class="h-4 w-4" />
                </div>
                {{ $page.props.flash.message }}
            </div>

            <div v-if="Object.keys($page.props.errors).length > 0" class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center text-rose-700 font-bold text-sm animate-in fade-in slide-in-from-top-4 duration-500">
                <div class="bg-rose-100 p-1 rounded-lg mr-3">
                    <AlertCircle class="h-4 w-4" />
                </div>
                <div>
                    <p v-for="error in $page.props.errors" :key="error">{{ error }}</p>
                </div>
            </div>

            <!-- Workshop Hero Section -->
            <div class="bg-white rounded-[3rem] overflow-hidden shadow-sm border border-gray-100">
                <div class="relative h-80 lg:h-96">
                    <img :src="workshopData.cover_photo_url" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 lg:p-12 w-full">
                        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">
                            <div class="space-y-4">
                                <div class="inline-flex items-center px-3 py-1 bg-indigo-600/90 backdrop-blur-sm text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg">
                                    Academy Workshop
                                </div>
                                <h1 class="text-4xl lg:text-6xl font-black text-white tracking-tight leading-tight">{{ workshopData.title }}</h1>
                                <div class="flex flex-wrap gap-8 text-gray-300 font-bold text-sm">
                                    <div class="flex items-center">
                                        <Calendar class="h-5 w-5 mr-3 text-indigo-400" />
                                        {{ formatDate(workshopData.starts_at) }}
                                    </div>
                                    <div class="flex items-center">
                                        <Clock class="h-5 w-5 mr-3 text-indigo-400" />
                                        {{ workshopData.duration_minutes }} Minutes
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button in Hero -->
                            <div class="flex-shrink-0">
                                <div v-if="workshopData.user_registration">
                                    <button
                                        @click="cancel"
                                        class="px-10 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white font-black text-sm uppercase tracking-widest rounded-[2rem] transition-all active:scale-95 shadow-2xl"
                                    >
                                        Cancel Registration
                                    </button>
                                </div>
                                <div v-else>
                                    <button
                                        @click="register"
                                        :class="workshopData.remaining_seats > 0 ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200' : 'bg-amber-500 hover:bg-amber-600 shadow-amber-200'"
                                        class="px-12 py-5 text-white font-black text-sm uppercase tracking-widest rounded-[2rem] transition-all shadow-2xl active:scale-95 flex items-center"
                                    >
                                        {{ workshopData.remaining_seats > 0 ? 'Register Now' : 'Join Waitlist' }}
                                        <ArrowRight class="ml-2 h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 lg:p-16">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                        <!-- Left: Description & Speaker -->
                        <div class="lg:col-span-2 space-y-16">
                            <!-- About Section -->
                            <div class="space-y-6">
                                <div class="flex items-center space-x-3 text-indigo-600">
                                    <Info class="h-6 w-6" />
                                    <h2 class="text-xl font-black uppercase tracking-widest">About this workshop</h2>
                                </div>
                                <div class="prose prose-indigo prose-lg max-w-none text-gray-600 leading-relaxed font-medium whitespace-pre-wrap">
                                    {{ workshopData.description }}
                                </div>
                            </div>

                            <!-- Speaker Profile Section -->
                            <div v-if="workshopData.speaker_name" class="space-y-8 pt-8 border-t border-gray-100">
                                <div class="flex items-center space-x-3 text-indigo-600">
                                    <Users class="h-6 w-6" />
                                    <h2 class="text-xl font-black uppercase tracking-widest">Your Instructor</h2>
                                </div>
                                <div class="bg-indigo-50/50 rounded-[3rem] p-10 flex flex-col md:flex-row items-center md:items-start gap-10 border border-indigo-100">
                                    <div class="relative">
                                        <img :src="workshopData.speaker_photo_url" class="h-32 w-32 rounded-[2.5rem] object-cover shadow-xl ring-4 ring-white" />
                                        <div class="absolute -bottom-2 -right-2 bg-emerald-500 p-2 rounded-2xl shadow-lg border-2 border-white">
                                            <ShieldCheck class="h-5 w-5 text-white" />
                                        </div>
                                    </div>
                                    <div class="space-y-4 text-center md:text-left flex-1">
                                        <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ workshopData.speaker_name }}</h3>
                                        <p class="text-gray-600 leading-relaxed text-lg font-medium">{{ workshopData.speaker_bio || 'Expert instructor at InterCourse Academy.' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Sidebar Stats -->
                        <div class="space-y-8">
                            <div class="bg-white rounded-[2.5rem] p-10 space-y-8 h-fit sticky top-12 shadow-sm border border-gray-100">
                                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Workshop Capacity</h3>
                                <div class="space-y-4">
                                    <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 transition-all hover:shadow-md">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 text-gray-500 font-bold">
                                                <Users class="h-5 w-5 text-indigo-500" />
                                                <span>Total Seats</span>
                                            </div>
                                            <span class="text-2xl font-black text-gray-900">{{ workshopData.capacity }}</span>
                                        </div>
                                    </div>
                                    <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 transition-all hover:shadow-md">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 text-gray-500 font-bold">
                                                <CheckCircle2 class="h-5 w-5 text-emerald-500" />
                                                <span>Confirmed</span>
                                            </div>
                                            <span class="text-2xl font-black text-emerald-600">{{ workshopData.confirmed_count }}</span>
                                        </div>
                                    </div>
                                    <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 transition-all hover:shadow-md">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 text-gray-500 font-bold">
                                                <Activity class="h-5 w-5 text-amber-500" />
                                                <span>Waitlist</span>
                                            </div>
                                            <span class="text-2xl font-black text-amber-600">{{ workshopData.capacity - workshopData.remaining_seats - workshopData.confirmed_count > 0 ? (workshopData.capacity - workshopData.remaining_seats - workshopData.confirmed_count) : 0 }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fill Rate Progress -->
                                <div class="pt-4">
                                    <div class="flex justify-between text-xs font-black uppercase tracking-widest text-gray-400 mb-3">
                                        <span>Current Fill Rate</span>
                                        <span>{{ Math.round((workshopData.confirmed_count / workshopData.capacity) * 100) }}%</span>
                                    </div>
                                    <div class="h-3 w-full bg-gray-200 rounded-full overflow-hidden">
                                        <div
                                            class="h-full bg-indigo-600 rounded-full transition-all duration-1000"
                                            :style="{ width: Math.round((workshopData.confirmed_count / workshopData.capacity) * 100) + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template>
