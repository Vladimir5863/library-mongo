<script setup>
import { usePage } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
const page = usePage();
</script>

<template>
    <Head title="Biblioteka Drvence" />
    <div class="bg-white shadow-sm">
        <nav
            class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-3"
        >
            <!-- Logo levo -->
            <Link href="/" class="flex items-center gap-3 group">
                <img
                    :src="'/storage/logo.png'"
                    alt="Drvence"
                    class="h-10 w-10 object-contain"
                />
                <span
                    class="text-2xl font-serif font-medium tracking-widest text-emerald-800 group-hover:text-emerald-600 transition-colors duration-300"
                >
                    Drvence
                </span>
            </Link>
            <!-- Linkovi desno -->
            <div
                class="flex flex-wrap items-center gap-4 justify-end"
                v-if="!page.props.auth.user"
            >
                <Link
                    :href="route('home')"
                    class="text-sm font-medium transition-colors duration-200"
                    :class="
                        $page.component === 'Home'
                            ? 'bg-emerald-800 text-white px-2 py-1 rounded-full hover:bg-emerald-600'
                            : 'text-emerald-800 hover:text-emerald-600'
                    "
                >
                    Home
                </Link>
                <Link
                    :href="route('register')"
                    class="text-sm font-medium transition-colors duration-200"
                    :class="
                        $page.component === 'Auth/Register'
                            ? 'bg-emerald-800 text-white px-2 py-1 rounded-full hover:bg-emerald-600'
                            : 'text-emerald-800 hover:text-emerald-600'
                    "
                >
                    Register
                </Link>
                <Link
                    :href="route('login')"
                    class="text-sm font-medium transition-colors duration-200"
                    :class="
                        $page.component === 'Auth/Login'
                            ? 'bg-emerald-800 text-white px-2 py-1 rounded-full hover:bg-emerald-600'
                            : 'text-emerald-800 hover:text-emerald-600'
                    "
                >
                    Login
                </Link>
            </div>

            <!-- Ako je ulogovan -->
            <div class="flex flex-wrap items-center gap-3 justify-end" v-else>
                <Link
                    v-if="page.props.auth.user"
                    :href="route('profile.index')"
                    class="flex items-center gap-2 text-sm font-medium text-emerald-800 hover:text-emerald-600 transition-colors"
                >
                    <img
                        v-if="page.props.auth.user?.avatar"
                        :src="page.props.auth.user.avatar"
                        alt="avatar"
                        class="h-8 w-8 rounded-full object-cover"
                    />
                    <span class="text-sm text-gray-600">{{
                        page.props.auth.user.name
                    }}</span>
                </Link>
                <Link
                    :href="route('home')"
                    class="text-sm font-medium transition-colors duration-200"
                    :class="
                        $page.component === 'Home'
                            ? 'bg-emerald-800 text-white px-2 py-1 rounded-full hover:bg-emerald-600'
                            : 'text-emerald-800 hover:text-emerald-600'
                    "
                >
                    Home
                </Link>
                <Link
                    v-if="page.props.auth.user?.userType === 'admin'"
                    :href="route('admin.index')"
                    :class="
                        $page.component === 'Admin/Index'
                            ? 'bg-emerald-800 text-white px-2 py-1 rounded-full hover:bg-emerald-600'
                            : 'text-emerald-800 hover:text-emerald-600'
                    "
                >
                    Admin
                </Link>
                <Link
                    v-if="
                        ['admin', 'librarian', 'postman'].includes(
                            page.props.auth.user?.userType,
                        )
                    "
                    :href="route('staff.loans')"
                    class="text-sm font-medium text-emerald-800 hover:text-emerald-600 transition-colors"
                    :class="
                        $page.component === 'Staff/Loans'
                            ? 'bg-emerald-800 text-white px-2 py-1 rounded-full hover:bg-emerald-600'
                            : 'text-emerald-800 hover:text-emerald-600'
                    "
                >
                    Pozajmice
                </Link>
                <Link
                    :href="route('subscription.index')"
                    class="text-sm font-medium transition-colors duration-200"
                    :class="
                        $page.component === 'Subscription/Index'
                            ? 'bg-emerald-800 text-white px-2 py-1 rounded-full hover:bg-emerald-600'
                            : 'text-emerald-800 hover:text-emerald-600'
                    "
                >
                    Pretplata
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm font-medium text-emerald-800 rounded-4xl px-4 py-2 hover:bg-emerald-800 hover:text-white transition-colors duration-200"
                >
                    Logout
                </Link>
            </div>
        </nav>
    </div>

    <slot />

    <footer class="bg-emerald-800 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Kolona 1 - Logo i opis -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <img
                            :src="'/storage/logo.png'"
                            alt="Drvence"
                            class="h-10 w-10 object-contain rounded-full"
                        />
                        <span
                            class="font-serif font-medium tracking-widest text-white"
                        >
                            Biblioteka Drvce
                        </span>
                    </div>
                    <p class="text-sm text-emerald-100 leading-relaxed">
                        Naše zajedničko mesto za znanje, otkrivanje i učenje.
                        Istražite našu bogatu kolekciju i pridružite se našoj
                        živahnoj književnoj zajednici.
                    </p>
                </div>

                <!-- Kolona 2 - Brzi linkovi -->
                <div class="flex flex-col gap-4">
                    <h3 class="font-semibold text-lg tracking-wide">
                        Brzi linkovi
                    </h3>
                    <div class="flex flex-col gap-3">
                        <Link
                            :href="route('books')"
                            class="text-emerald-100 hover:text-white underline underline-offset-4 transition-colors duration-200 w-fit"
                        >
                            Pretraga knjiga
                        </Link>
                        <Link
                            :href="route('news.index')"
                            class="text-emerald-100 hover:text-white underline underline-offset-4 transition-colors duration-200 w-fit"
                        >
                            Pregled vesti
                        </Link>
                        <Link
                            :href="route('loans.index')"
                            class="text-emerald-100 hover:text-white underline underline-offset-4 transition-colors duration-200 w-fit"
                        >
                            Moje pozajmice
                        </Link>
                    </div>
                </div>

                <!-- Kolona 3 - Kontakt -->
                <div class="flex flex-col gap-4">
                    <h3 class="font-semibold text-lg tracking-wide">
                        Kontaktirajte nas
                    </h3>
                    <div class="flex flex-col gap-3">
                        <div
                            class="flex items-center gap-3 text-sm text-emerald-100"
                        >
                            <span class="text-xl">📍</span>
                            <span>24 Nemanjina, Čačak</span>
                        </div>
                        <div
                            class="flex items-center gap-3 text-sm text-emerald-100"
                        >
                            <span class="text-xl">📞</span>
                            <span>123 / 456 - 789</span>
                        </div>
                        <div
                            class="flex items-center gap-3 text-sm text-emerald-100"
                        >
                            <span class="text-xl">✉️</span>
                            <span>drvence@gmail.com</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div
                class="border-t border-emerald-700 mt-10 pt-6 text-center text-xs text-emerald-300"
            >
                © Biblioteka Drvce. Sva prava zadržana.
            </div>
        </div>
    </footer>
</template>
