<script setup>
defineProps({
    book: Object,
});
</script>

<template>
    <div
        class="w-full rounded-2xl overflow-hidden shadow-sm bg-white flex flex-col hover:shadow-md transition-shadow duration-200"
    >
        <!-- Slika -->
        <div class="relative">
            <img
                v-if="book.preview_image"
                :src="'/storage/' + book.preview_image"
                :alt="book.title"
                class="w-full h-40 sm:h-64 object-cover"
            />
            <div
                v-else
                class="w-full h-40 sm:h-64 bg-gray-200 flex items-center justify-center"
            >
                <span class="text-4xl">📚</span>
            </div>
        </div>

        <!-- Info -->
        <div class="p-4 flex flex-col gap-1 flex-1">
            <p class="text-xs text-gray-500 italic">{{ book.genre }}</p>
            <p class="text-sm font-bold text-gray-800 line-clamp-2">
                {{ book.title }}
            </p>
            <p class="text-xs text-gray-600 line-clamp-1">{{ book.author }}</p>

            <!-- Dostupnost -->
            <div class="flex flex-wrap gap-3 text-xs text-gray-500 mt-1">
                <span>🛒 {{ book.remainingForSell }} za kupovinu</span>
                <span>📖 {{ book.remainingForLoan }} za izdavanje</span>
            </div>

            <!-- Preview i Detalji -->
            <div
                class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100"
            >
                <Link
                    v-if="book.preview"
                    :href="'/storage/' + book.preview"
                    target="_blank"
                    class="text-xs text-emerald-700 hover:underline font-medium"
                >
                    📄 Preview
                </Link>

                <Link
                    :href="'/books/' + book.id"
                    class="text-xs font-semibold text-emerald-700 hover:text-emerald-500"
                >
                    Detalji
                </Link>
            </div>
        </div>
    </div>
</template>
