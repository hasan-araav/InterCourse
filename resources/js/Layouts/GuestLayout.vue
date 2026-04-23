<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { Zap, ShieldCheck, Star } from 'lucide-vue-next';
</script>

<template>
    <div class="min-h-screen flex flex-col md:flex-row bg-white font-sans selection:bg-indigo-100 selection:text-indigo-700">
        <!-- Left Side: Informational/Branding (Hidden on mobile) -->
        <div class="hidden md:flex md:w-1/2 bg-indigo-600 relative overflow-hidden flex-col justify-center px-12 lg:px-24">
            <div class="absolute inset-0 -z-0 opacity-10">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)" />
                </svg>
            </div>
            
            <div class="relative z-10">
                <Link href="/" class="inline-flex items-center gap-2 mb-12">
                    <ApplicationLogo class="h-10 w-10 text-white" />
                    <span class="text-2xl font-bold text-white tracking-tight">InterCourse</span>
                </Link>

                <h2 class="text-4xl font-extrabold text-white mb-6 leading-tight">
                    Join the premier community for professional growth.
                </h2>
                
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="mt-1 bg-indigo-500/30 p-2 rounded-lg">
                            <Zap class="w-5 h-5 text-indigo-100" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Real-time Learning</h3>
                            <p class="text-indigo-100 text-sm leading-relaxed">Stay ahead with instant updates and live workshop interactions.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="mt-1 bg-indigo-500/30 p-2 rounded-lg">
                            <ShieldCheck class="w-5 h-5 text-indigo-100" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Verified Expertise</h3>
                            <p class="text-indigo-100 text-sm leading-relaxed">Learn from industry leaders with proven track records.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="mt-1 bg-indigo-500/30 p-2 rounded-lg">
                            <Star class="w-5 h-5 text-indigo-100" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Curated Workshops</h3>
                            <p class="text-indigo-100 text-sm leading-relaxed">Access a selection of high-impact professional development sessions.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decorative Circles -->
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-400 rounded-full blur-3xl opacity-30"></div>
        </div>

        <!-- Right Side: Auth Form -->
        <div class="flex-1 flex flex-col justify-center px-6 py-12 lg:px-24 bg-gray-50/50">
            <div class="md:hidden flex justify-center mb-8">
                <Link href="/" class="flex items-center gap-2">
                    <ApplicationLogo class="h-10 w-10 text-indigo-600" />
                    <span class="text-2xl font-bold text-indigo-600 tracking-tight">InterCourse</span>
                </Link>
            </div>

            <div class="mx-auto w-full max-w-md">
                <slot />
            </div>
            
            <footer class="mt-auto pt-8 text-center text-sm text-gray-400">
                &copy; 2026 InterCourse. All rights reserved.
            </footer>
        </div>
    </div>
</template>
