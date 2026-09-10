<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-6xl mx-auto flex flex-col gap-8">
            <h1 class="text-2xl font-serif font-semibold text-gray-800">
                Admin Panel
            </h1>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                <div
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-2"
                >
                    <p class="text-xs text-gray-400">Korisnici</p>
                    <p class="text-3xl font-bold text-emerald-700">
                        {{ stats.users }}
                    </p>
                </div>
                <div
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-2"
                >
                    <p class="text-xs text-gray-400">Knjige</p>
                    <p class="text-3xl font-bold text-emerald-700">
                        {{ stats.books }}
                    </p>
                </div>
                <Link
                    :href="route('admin.bookalert')"
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center gap-3 transition-all duration-300 border-2"
                    :class="
                        stats.lowStockCount > 0
                            ? 'border-red-100 hover:border-red-300 shadow-red-50/50'
                            : 'border-transparent hover:border-emerald-100'
                    "
                >
                    <div class="relative">
                        <span class="text-3xl">⚠️</span>
                        <span
                            v-if="stats.lowStockCount > 0"
                            class="absolute -top-1 -right-1 flex h-4 w-4"
                        >
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white"
                            ></span>
                        </span>
                    </div>
                    <p class="text-sm font-medium text-gray-700">Zalihe</p>

                    <span
                        v-if="stats.lowStockCount > 0"
                        class="text-[10px] font-bold text-red-500 uppercase tracking-wider"
                    >
                        {{ stats.lowStockCount }} kritično
                    </span>
                </Link>
                <div
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-2"
                >
                    <p class="text-xs text-gray-400">Aktivne pretplate</p>
                    <p class="text-3xl font-bold text-emerald-700">
                        {{ stats.activeSubscriptions }}
                    </p>
                </div>
                <div
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-2"
                >
                    <p class="text-xs text-gray-400">Aktivne pozajmice</p>
                    <p class="text-3xl font-bold text-emerald-700">
                        {{ stats.activeLoans }}
                    </p>
                </div>
            </div>

            <!-- Navigacija -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <Link
                    :href="route('admin.users')"
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center gap-3 hover:shadow-md transition-shadow"
                >
                    <span class="text-3xl">👥</span>
                    <p class="text-sm font-medium text-gray-700">Korisnici</p>
                </Link>
                <Link
                    :href="route('admin.subscriptions')"
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center gap-3 hover:shadow-md transition-shadow"
                >
                    <span class="text-3xl">💳</span>
                    <p class="text-sm font-medium text-gray-700">Pretplate</p>
                </Link>
                <Link
                    :href="route('admin.books')"
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center gap-3 hover:shadow-md transition-shadow"
                >
                    <span class="text-3xl">📚</span>
                    <p class="text-sm font-medium text-gray-700">Knjige</p>
                </Link>
                <Link
                    :href="route('admin.loans')"
                    class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center gap-3 hover:shadow-md transition-shadow"
                >
                    <span class="text-3xl">📋</span>
                    <p class="text-sm font-medium text-gray-700">Pozajmice</p>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    stats: Object,
});
</script>
