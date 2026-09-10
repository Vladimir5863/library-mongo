<template>
    <div
        class="min-h-screen bg-emerald-700 flex flex-col items-center justify-center text-white text-center px-6 py-20 gap-8"
    >
        <!-- Badge -->
        <div
            class="flex items-center gap-2 bg-emerald-500 px-5 py-2 rounded-full text-sm font-medium shadow-md"
        >
            <img
                :src="'/storage/logo.png'"
                alt="logo"
                class="h-6 w-6 object-contain"
            />
            Dobrodošli u biblioteku drvce
        </div>

        <!-- Naslov -->
        <div class="flex flex-col gap-3">
            <h1 class="text-4xl font-semibold tracking-tight">
                Otkrijte svoje sledeće
            </h1>
            <h1 class="text-4xl font-semibold tracking-tight">
                sjajno čitanje
            </h1>
        </div>

        <!-- Opis -->
        <p class="text-emerald-100 max-w-md text-base leading-relaxed">
            Istražite našu bogatu kolekciju knjiga, budite u toku sa novostima
            iz biblioteke i upravljajte svojim zaduženjima – sve na jednom
            mestu.
        </p>

        <!-- Dugmad -->
        <div class="flex items-center gap-4 mt-2">
            <Link
                :href="route('books')"
                class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-medium px-6 py-3 rounded-full transition-colors duration-200 shadow-md"
            >
                <span class="text-lg">🔍</span>
                Pretraga knjiga
            </Link>
            <Link
                :href="route('news.index')"
                class="flex items-center gap-2 border border-white text-white hover:bg-emerald-600 font-medium px-6 py-3 rounded-full transition-colors duration-200"
            >
                Najnovije vesti
                <span>⟶</span>
            </Link>
        </div>
    </div>
</template>
