<template>
    <div v-if="books.length > 0" class="bg-white py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-serif font-semibold text-gray-800">
                    Preporučeno za vas
                </h2>
                <Link
                    :href="route('books')"
                    class="text-sm text-emerald-700 hover:text-emerald-500 font-medium"
                >
                    Vidi sve →
                </Link>
            </div>
            <div
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6"
            >
                <Book v-for="book in books" :key="book.bookId" :book="book" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import Book from "@/Pages/Components/Book.vue";

defineProps({
    books: {
        type: Array,
        default: () => [],
    },
});
</script>
