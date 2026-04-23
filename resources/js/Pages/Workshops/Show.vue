<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    ChevronLeft, 
    Users, 
    Clock, 
    Calendar, 
    MapPin, 
    Mail, 
    UserCheck,
    ArrowRight
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
</script>

<template>
    <Head :title="workshop.title" />

    <AdminLayout>
        <div class="space-y-8 pb-12">
            <!-- Breadcrumbs / Back -->
            <Link :href="route('admin.dashboard')" class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors group">
                <ChevronLeft class="h-4 w-4 mr-1 group-hover:-translate-x-1 transition-transform" />
                Back to Dashboard
            </Link>

            <!-- Workshop Header Card -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="space-y-4">
                        <div class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-black uppercase tracking-widest rounded-full">
                            Workshop Details
                        </div>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tight">{{ workshop.title }}</h1>
                        <div class="flex flex-wrap gap-6 text-gray-500 font-medium">
                            <div class="flex items-center">
                                <Calendar class="h-5 w-5 mr-2 text-indigo-500" />
                                {{ formatDate(workshop.starts_at) }}
                            </div>
                            <div class="flex items-center">
                                <Clock class="h-5 w-5 mr-2 text-indigo-500" />
                                {{ workshop.duration_minutes }} Minutes
                            </div>
                            <div class="flex items-center">
                                <Users class="h-5 w-5 mr-2 text-indigo-500" />
                                Capacity: {{ workshop.capacity }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <Link :href="route('admin.workshops.edit', workshop.id)" class="px-6 py-3 bg-white border-2 border-gray-100 text-gray-700 font-bold rounded-2xl hover:bg-gray-50 transition-all shadow-sm">
                            Edit Workshop
                        </Link>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <p class="text-gray-600 leading-relaxed text-lg">{{ workshop.description }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Attendees Section -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-indigo-50/30 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-indigo-600 rounded-xl">
                                <UserCheck class="h-5 w-5 text-white" />
                            </div>
                            <h2 class="text-xl font-black text-gray-900">Confirmed Attendees</h2>
                        </div>
                        <span class="px-3 py-1 bg-white text-indigo-700 text-sm font-black rounded-full shadow-sm border border-indigo-100">
                            {{ workshop.attendees.length }} / {{ workshop.capacity }}
                        </span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-if="workshop.attendees.length === 0" class="p-12 text-center">
                            <Users class="h-12 w-12 text-gray-200 mx-auto mb-4" />
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">No confirmed attendees yet</p>
                        </div>
                        <div v-for="user in workshop.attendees" :key="user.id" class="p-6 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <img :src="user.photo" class="h-12 w-12 rounded-2xl shadow-sm group-hover:scale-105 transition-transform" />
                                    <div>
                                        <div class="font-black text-gray-900">{{ user.name }}</div>
                                        <div class="flex items-center text-xs text-gray-400 font-bold mt-0.5">
                                            <Mail class="h-3 w-3 mr-1" />
                                            {{ user.email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Registered</div>
                                    <div class="text-xs font-bold text-gray-600">{{ user.registered_at }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waitlist Section -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 bg-amber-50/30 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-amber-500 rounded-xl">
                                <Clock class="h-5 w-5 text-white" />
                            </div>
                            <h2 class="text-xl font-black text-gray-900">Waitlist</h2>
                        </div>
                        <span class="px-3 py-1 bg-white text-amber-700 text-sm font-black rounded-full shadow-sm border border-amber-100">
                            {{ workshop.waitlist.length }} Pending
                        </span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div v-if="workshop.waitlist.length === 0" class="p-12 text-center">
                            <Clock class="h-12 w-12 text-gray-200 mx-auto mb-4" />
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Waitlist is empty</p>
                        </div>
                        <div v-for="user in workshop.waitlist" :key="user.id" class="p-6 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="relative">
                                        <img :src="user.photo" class="h-12 w-12 rounded-2xl shadow-sm group-hover:scale-105 transition-transform" />
                                        <div class="absolute -top-2 -right-2 bg-amber-500 text-white text-[10px] font-black h-5 w-5 flex items-center justify-center rounded-full border-2 border-white">
                                            {{ user.position }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-black text-gray-900">{{ user.name }}</div>
                                        <div class="flex items-center text-xs text-gray-400 font-bold mt-0.5">
                                            <Mail class="h-3 w-3 mr-1" />
                                            {{ user.email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Waitlisted</div>
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
