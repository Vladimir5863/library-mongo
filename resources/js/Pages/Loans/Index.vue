<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl font-serif font-semibold text-gray-800 mb-6">
                Moje pozajmice
            </h1>

            <EmptyState
                v-if="loans.length === 0"
                icon="📭"
                title="Nemate aktivnih pozajmica"
            />

            <div v-else class="flex flex-col gap-4">
                <div
                    v-for="loan in loans"
                    :key="loan.loanId"
                    class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-6"
                >
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">
                            {{ loan.book.title }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ loan.book.author }}
                        </p>
                        <div class="flex gap-4 mt-2 text-xs text-gray-400">
                            <span>📅 Uzeto: {{ loan.loanDate }}</span>
                            <span>⏰ Rok: {{ loan.endReturnDate }}</span>
                        </div>
                    </div>
                    <span
                        class="text-xs px-3 py-1 rounded-full font-medium"
                        :class="
                            loan.status === 'returned_on_time' ||
                            loan.status === 'returned_late'
                                ? 'bg-gray-100 text-gray-500'
                                : 'bg-emerald-100 text-emerald-700'
                        "
                    >
                        {{ loan.status }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import EmptyState from "@/Pages/Components/EmptyState.vue";

defineProps({
    loans: {
        type: Array,
        default: () => [],
    },
});
</script>
