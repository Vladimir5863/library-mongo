<script setup>
import Hero from "./Components/Hero.vue";
import Book from "./Components/Book.vue";
import { usePage } from "@inertiajs/vue3";
import Recommendations from "@/Pages/Components/Recommendations.vue";
import NewsCard from "@/Pages/Components/News.vue";

const page = usePage();
defineProps({
    books: Array,
    latestNews: Array,
    recommendations: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Hero />
    <!-- Random knjige -->
    <div class="bg-white py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <!-- Naslov sekcije -->
            <div class="flex items-center justify-center mb-8">
                <h2
                    class="text-2xl font-serif font-semibold text-gray-800 text-center"
                >
                    Preporučene knjige
                </h2>
            </div>

            <!-- Knjige grid -->
            <div
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6"
            >
                <Book v-for="book in books" :key="book.bookId" :book="book" />
            </div>
        </div>
    </div>
    <!-- Najnovije vesti -->
    <div class="bg-gray-50 py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-serif font-semibold text-gray-800">
                    Najnovije vesti
                </h2>
                <Link
                    :href="route('news.index')"
                    class="text-sm text-emerald-700 hover:text-emerald-500 font-medium"
                >
                    Vidi sve →
                </Link>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <NewsCard
                    v-for="item in latestNews"
                    :key="item.newsId"
                    :item="item"
                    variant="compact"
                    :show-delete="false"
                />
            </div>
        </div>
    </div>
    <!-- Preporučene knjige (samo za ulogovane) -->
    <Recommendations v-if="page.props.auth.user" :books="recommendations" />
</template>
