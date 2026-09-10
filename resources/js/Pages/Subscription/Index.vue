<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-4xl mx-auto flex flex-col gap-8">
            <!-- Header red -->
            <div class="flex items-center justify-between">
                <!-- Aktivna badge -->
                <div
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium"
                    :class="
                        hasActive
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-red-100 text-red-600'
                    "
                >
                    <span>{{ hasActive ? "✅" : "❌" }}</span>
                    {{
                        hasActive
                            ? "Aktivna pretplata"
                            : "Nema aktivne pretplate"
                    }}
                    <span
                        v-if="hasActive"
                        class="text-xs font-normal text-emerald-600"
                    >
                        (ističe {{ subscription.endDate }})
                    </span>
                </div>

                <!-- Dugme -->
                <div class="flex gap-3">
                    <button
                        v-if="hasActive"
                        @click="cancelSubscription"
                        class="text-sm font-medium text-red-500 hover:text-red-400 underline transition-colors"
                    >
                        Otkaži pretplatu
                    </button>
                    <Link
                        v-else
                        :href="route('subscription.create')"
                        class="bg-emerald-700 hover:bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors"
                    >
                        Pretplati se
                    </Link>
                </div>
            </div>

            <!-- Istorija tabela -->
            <div class="bg-white rounded-2xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Istorija pretplata
                    </h2>
                </div>

                <div
                    v-if="history.length === 0"
                    class="flex flex-col items-center justify-center py-16 text-gray-400"
                >
                    <span class="text-4xl mb-3">📭</span>
                    <p class="text-sm">Nema istorije pretplata</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-max w-full">
                        <thead
                            class="bg-gray-50 text-xs text-gray-500 uppercase"
                        >
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    Datum početka
                                </th>
                                <th class="px-6 py-3 text-left">
                                    Datum isteka
                                </th>
                                <th class="px-6 py-3 text-left">Cena</th>
                                <th class="px-6 py-3 text-left">
                                    Broj računa
                                </th>
                                <th class="px-6 py-3 text-left">
                                    Auto-renewal
                                </th>
                                <th class="px-6 py-3 text-left">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="item in history"
                                :key="item.subscriptionId"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td
                                    class="px-6 py-4 text-sm text-gray-700"
                                >
                                    {{ item.startDate }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-gray-700"
                                >
                                    {{ item.endDate }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-gray-700"
                                >
                                    {{ item.price / 100 }} RSD
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-gray-500"
                                >
                                    {{ item.accountNumber }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium"
                                        :class="
                                            item.autoRenew
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-gray-100 text-gray-500'
                                        "
                                    >
                                        {{ item.autoRenew ? "Da" : "Ne" }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium"
                                        :class="
                                            item.active
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-gray-100 text-gray-500'
                                        "
                                    >
                                        {{
                                            item.active ? "Aktivna" : "Istekla"
                                        }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";

defineProps({
    subscription: Object,
    hasActive: Boolean,
    history: {
        type: Array,
        default: () => [],
    },
});

const cancelForm = useForm({});
const cancelSubscription = () => {
    if (confirm("Da li ste sigurni da želite da otkažete pretplatu?")) {
        cancelForm.delete(route("subscription.cancel"));
    }
};
</script>
