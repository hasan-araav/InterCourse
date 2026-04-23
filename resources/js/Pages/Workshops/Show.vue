<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    Users,
    Clock,
    Calendar,
    Mail,
    UserCheck,
    CheckCircle2,
    Activity,
    Info,
    LayoutGrid,
    ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
    workshop: Object,
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

const getEngagementMetrics = () => {
    const total = props.workshop.attendees.length + props.workshop.waitlist.length;
    const fillRate = props.workshop.capacity > 0
        ? Math.round((props.workshop.attendees.length / props.workshop.capacity) * 100)
        : 0;
    return { total, fillRate };
};

const metrics = getEngagementMetrics();
</script>

<template>
    <Head :title="workshop.title" />

    <AdminLayout>
        <div class="space-y-8 pb-12">
            <!-- Breadcrumbs / Back -->
            <div class="flex items-center justify-between">
                <Link :href="route('admin.workshops.index')" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors group">
                    <ChevronLeft class="h-4 w-4 mr-1 group-hover:-translate-x-1 transition-transform" />
                    Back to Inventory
                </Link>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    Workshop ID: #{{ workshop.id }}
                </div>
            </div>

            <!-- Workshop Hero Section -->
            <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-gray-100">
                <div class="relative h-64 lg:h-80">
                    <img :src="workshop.cover_photo" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 lg:p-12 w-full">
                        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                            <div class="space-y-3">
                                <span class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-full">
                                    Live Workshop
                                </span>
                                <h1 class="text-3xl lg:text-5xl font-black text-white tracking-tight">{{ workshop.title }}</h1>
                                <div class="flex flex-wrap gap-6 text-gray-300 font-bold text-sm">
                                    <div class="flex items-center">
                                        <Calendar class="h-4 w-4 mr-2 text-indigo-400" />
                                        {{ formatDate(workshop.starts_at) }}
                                    </div>
                                    <div class="flex items-center">
                                        <Clock class="h-4 w-4 mr-2 text-indigo-400" />
                                        {{ workshop.duration_minutes }} Minutes
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-8 lg:p-12">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                        <div class="lg:col-span-2 space-y-6">
                            <div class="flex items-center space-x-2 text-indigo-600">
                                <Info class="h-5 w-5" />
                                <h2 class="text-lg font-black uppercase tracking-widest">About this workshop</h2>
                            </div>
                            <p class="text-gray-600 leading-relaxed text-lg whitespace-pre-wrap">{{ workshop.description }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-3xl p-8 space-y-6">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">At a Glance</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-white rounded-2xl shadow-sm">
                                    <div class="flex items-center space-x-3 text-gray-600 font-bold text-sm">
                                        <Users class="h-4 w-4 text-indigo-500" />
                                        <span>Capacity</span>
                                    </div>
                                    <span class="text-lg font-black text-gray-900">{{ workshop.capacity }}</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-white rounded-2xl shadow-sm">
                                    <div class="flex items-center space-x-3 text-gray-600 font-bold text-sm">
                                        <CheckCircle2 class="h-4 w-4 text-emerald-500" />
                                        <span>Confirmed</span>
                                    </div>
                                    <span class="text-lg font-black text-emerald-600">{{ workshop.attendees.length }}</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-white rounded-2xl shadow-sm">
                                    <div class="flex items-center space-x-3 text-gray-600 font-bold text-sm">
                                        <Activity class="h-4 w-4 text-amber-500" />
                                        <span>Waitlist</span>
                                    </div>
                                    <span class="text-lg font-black text-amber-600">{{ workshop.waitlist.length }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Attendees Section -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-indigo-50/30 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-indigo-600 rounded-xl">
                                <UserCheck class="h-5 w-5 text-white" />
                            </div>
                            <h2 class="text-xl font-black text-gray-900">Attendance List</h2>
                        </div>
                        <span class="px-4 py-1.5 bg-white text-indigo-700 text-xs font-black rounded-full shadow-sm border border-indigo-100">
                            {{ workshop.attendees.length }} Confirmed
                        </span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-if="workshop.attendees.length === 0" class="p-12 text-center">
                            <Users class="h-12 w-12 text-gray-200 mx-auto mb-4" />
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">No confirmed attendees</p>
                        </div>
                        <div v-for="user in workshop.attendees" :key="user.id" class="p-6 hover:bg-gray-50 transition-all group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <img :src="user.photo" class="h-12 w-12 rounded-2xl shadow-sm group-hover:scale-105 transition-transform object-cover" />
                                    <div>
                                        <Link :href="route('admin.users.show', user.id)" class="font-black text-gray-900 hover:text-indigo-600 transition-colors">{{ user.name }}</Link>
                                        <div class="flex items-center text-xs text-gray-400 font-bold mt-0.5">
                                            <Mail class="h-3 w-3 mr-1" />
                                            {{ user.email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Enrolled</div>
                                    <div class="text-xs font-bold text-gray-600">{{ user.registered_at }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waitlist Section -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-amber-50/30 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-amber-500 rounded-xl">
                                <Clock class="h-5 w-5 text-white" />
                            </div>
                            <h2 class="text-xl font-black text-gray-900">Waitlist Queue</h2>
                        </div>
                        <span class="px-4 py-1.5 bg-white text-amber-700 text-xs font-black rounded-full shadow-sm border border-amber-100">
                            {{ workshop.waitlist.length }} Pending
                        </span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-if="workshop.waitlist.length === 0" class="p-12 text-center">
                            <Clock class="h-12 w-12 text-gray-200 mx-auto mb-4" />
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Waitlist is currently empty</p>
                        </div>
                        <div v-for="user in workshop.waitlist" :key="user.id" class="p-6 hover:bg-gray-50 transition-all group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="relative">
                                        <img :src="user.photo" class="h-12 w-12 rounded-2xl shadow-sm group-hover:scale-105 transition-transform object-cover" />
                                        <div class="absolute -top-2 -right-2 bg-amber-500 text-white text-[10px] font-black h-5 w-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm">
                                            {{ user.position }}
                                        </div>
                                    </div>
                                    <div>
                                        <Link :href="route('admin.users.show', user.id)" class="font-black text-gray-900 hover:text-indigo-600 transition-colors">{{ user.name }}</Link>
                                        <div class="flex items-center text-xs text-gray-400 font-bold mt-0.5">
                                            <Mail class="h-3 w-3 mr-1" />
                                            {{ user.email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Queued</div>
                                    <div class="text-xs font-bold text-gray-600">{{ user.registered_at }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
