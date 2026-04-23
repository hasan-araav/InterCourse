<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import {
    Calendar,
    Clock,
    ChevronRight,
    CheckCircle2,
    AlertCircle,
    BookOpen,
    LayoutGrid,
    Search,
    ArrowRight,
    Activity,
    MapPin,
    Users
} from 'lucide-vue-next';
import EmployeeLayout from '@/Layouts/EmployeeLayout.vue';
import { usePolling } from '@/Composables/usePolling';

const props = defineProps({
    workshops: Array,
});

const workshopsData = ref([...props.workshops]);
const search = ref('');
const pulseStates = ref({});
let echoChannels = [];

// Watch for prop changes and update local state
watch(() => props.workshops, (newWorkshops) => {
    workshopsData.value = [...newWorkshops];
}, { deep: true });

// Start polling as a fallback if WebSockets are not active
usePolling(10000);

onMounted(() => {
    if (window.Echo) {
        workshopsData.value.forEach((workshop) => {
            window.Echo.channel('workshops.' + workshop.id)
                .listen('.registration.updated', (data) => {
                    const index = workshopsData.value.findIndex(w => w.id === data.id);
                    if (index !== -1) {
                        workshopsData.value[index].remaining_seats = data.capacity - data.confirmed_count;
                        workshopsData.value[index].confirmed_count = data.confirmed_count;
                        pulseStates.value[workshop.id] = true;
                        setTimeout(() => pulseStates.value[workshop.id] = false, 1000);
                    }
                });
            echoChannels.push('workshops.' + workshop.id);
        });
    }
});

onUnmounted(() => {
    echoChannels.forEach((channelName) => window.Echo.leave(channelName));
});

const register = (workshopId) => {
    router.post(route('workshops.register', workshopId), {}, {
        preserveScroll: true,
    });
};

const cancel = (workshopId) => {
    if (confirm('Are you sure you want to cancel your registration?')) {
        router.delete(route('workshops.cancel', workshopId), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString([], {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const filteredWorkshops = ref(workshopsData);
watch([search, workshopsData], () => {
    filteredWorkshops.value = workshopsData.value.filter(w =>
        w.title.toLowerCase().includes(search.value.toLowerCase()) ||
        w.description.toLowerCase().includes(search.value.toLowerCase())
    );
}, { immediate: true });
</script>

<template>
    <Head title="Learning Hub" />

    <EmployeeLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 py-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Learning Hub</h2>
                    <p class="text-gray-500 font-medium mt-1">Discover and enroll in professional development workshops.</p>
                </div>
                <div class="relative w-full md:w-80">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search workshops..."
                        class="w-full pl-12 pr-4 py-3 bg-white border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm font-medium shadow-sm"
                    />
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

            <!-- Workshop Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="workshop in filteredWorkshops" :key="workshop.id"
                    class="group bg-white rounded-[2rem] overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col"
                >
                    <!-- Cover Image -->
                    <div class="relative h-56 overflow-hidden">
                        <img :src="workshop.cover_photo_url" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                        <div class="absolute top-4 right-4">
                            <span
                                :class="[
                                    workshop.user_registration?.status === 'confirmed' ? 'bg-emerald-500 text-white' :
                                    workshop.user_registration?.status === 'waitlisted' ? 'bg-amber-500 text-white' :
                                    'bg-white/90 text-gray-900 backdrop-blur-sm',
                                    'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm'
                                ]"
                            >
                                {{ workshop.user_registration ? (workshop.user_registration.status === 'confirmed' ? 'Enrolled' : 'Waitlisted #' + workshop.user_registration.position) : 'Available' }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-8 flex-1 flex flex-col">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">
                                    {{ workshop.duration_minutes }} Mins
                                </span>
                                <div v-if="workshop.remaining_seats <= 2 && workshop.remaining_seats > 0" class="flex items-center text-rose-600 text-[10px] font-black uppercase tracking-widest">
                                    <Activity class="h-3 w-3 mr-1" />
                                    Filling Fast
                                </div>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-1">{{ workshop.title }}</h3>
                            <p class="mt-3 text-gray-500 text-sm font-medium line-clamp-2 leading-relaxed">
                                {{ workshop.description }}
                            </p>

                            <div class="mt-6 space-y-3">
                                <div class="flex items-center text-sm font-bold text-gray-600">
                                    <Calendar class="h-4 w-4 mr-3 text-indigo-500" />
                                    {{ formatDate(workshop.starts_at) }}
                                </div>
                                <div class="flex items-center text-sm font-bold text-gray-600">
                                    <Users class="h-4 w-4 mr-3 text-indigo-500" />
                                    <span :class="{ 'animate-pulse text-indigo-600 font-black': pulseStates[workshop.id] }">
                                        {{ workshop.remaining_seats }} / {{ workshop.capacity }} seats available
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 pt-6 border-t border-gray-50 flex items-center justify-between gap-4">
                            <Link :href="route('workshops.show', workshop.id)" class="text-sm font-black text-indigo-600 hover:text-indigo-700 flex items-center group/link">
                                View Details
                                <ArrowRight class="h-4 w-4 ml-1 group-hover/link:translate-x-1 transition-transform" />
                            </Link>

                            <div v-if="workshop.user_registration">
                                <button @click="cancel(workshop.id)" class="text-xs font-black text-rose-500 hover:text-rose-700 transition-colors uppercase tracking-widest">
                                    Cancel
                                </button>
                            </div>
                            <div v-else>
                                <button
                                    @click="register(workshop.id)"
                                    :class="workshop.remaining_seats > 0 ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-100' : 'bg-amber-500 hover:bg-amber-600 shadow-amber-100'"
                                    class="px-6 py-2.5 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95"
                                >
                                    {{ workshop.remaining_seats > 0 ? 'Register' : 'Waitlist' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredWorkshops.length === 0" class="col-span-full bg-white rounded-[2rem] p-20 text-center border-2 border-dashed border-gray-100">
                    <div class="bg-gray-50 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <BookOpen class="h-10 w-10 text-gray-300" />
                    </div>
                    <h3 class="text-xl font-black text-gray-900">No workshops found</h3>
                    <p class="text-gray-400 font-medium mt-2">Try adjusting your search to find what you're looking for.</p>
                </div>
            </div>
        </div>
    </EmployeeLayout>
</template>
