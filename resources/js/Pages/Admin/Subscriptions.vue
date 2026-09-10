<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-6xl mx-auto flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.index')"
                    class="text-sm text-emerald-700 hover:underline"
                    >← Nazad</Link
                >
                <h1
                    class="text-2xl font-serif font-semibold text-gray-800 tracking-tight"
                >
                    Pretplate
                </h1>
            </div>

            <div class="bg-white rounded-2xl shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full">
                    <thead
                        class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider"
                    >
                        <tr>
                            <th class="px-6 py-3 text-left">Korisnik</th>
                            <th class="px-6 py-3 text-left">Početak</th>
                            <th class="px-6 py-3 text-left">Istek</th>
                            <th class="px-6 py-3 text-left">Cena</th>
                            <th class="px-6 py-3 text-left">Broj računa</th>
                            <th class="px-6 py-3 text-left">Auto</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="sub in subscriptions.data"
                            :key="sub.subscriptionId"
                            class="hover:bg-gray-50"
                        >
                            <td
                                class="px-6 py-4 text-sm font-medium text-gray-800"
                            >
                                {{ sub.user?.name }} {{ sub.user?.surname }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ sub.startDate }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ sub.endDate }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-gray-700 font-medium"
                            >
                                {{ sub.price / 100 }} RSD
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ sub.accountNumber }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs px-2 py-1 rounded-full"
                                    :class="
                                        sub.autoRenew
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-gray-100 text-gray-500'
                                    "
                                >
                                    {{ sub.autoRenew ? "Da" : "Ne" }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs px-2 py-1 rounded-full font-medium"
                                    :class="
                                        sub.active
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-gray-100 text-gray-500'
                                    "
                                >
                                    {{ sub.active ? "Aktivna" : "Istekla" }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>

            <Pagination :data="subscriptions" />
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import Pagination from "@/Pages/Components/Pagination.vue";

defineProps({
    subscriptions: Object,
});
</script>
