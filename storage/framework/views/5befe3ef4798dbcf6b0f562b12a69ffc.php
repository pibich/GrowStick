<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta name="description" content="<?php echo e(__('nav.description')); ?>">
        <meta name="theme-color" content="#f4efe6" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#171310" media="(prefers-color-scheme: dark)">

        <meta property="og:site_name" content="<?php echo e(config('app.name')); ?>">
        <meta property="og:title" content="<?php echo e($title ?? config('app.name')); ?>">
        <meta property="og:description" content="<?php echo e(__('nav.description')); ?>">
        <meta property="og:type" content="website">

        <?php
            $pageTitle = match (true) {
                request()->routeIs('notes.wall') => __('notes.title'),
                request()->routeIs('login') => __('auth.login'),
                request()->routeIs('register') => __('auth.register'),
                request()->routeIs('password.request') => __('auth.forgot'),
                request()->routeIs('password.reset') => __('auth.reset_password'),
                default => $title ?? config('app.name'),
            };
        ?>
        <title><?php echo e($pageTitle); ?></title>

        <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon.svg')); ?>">

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    </head>
    <body class="min-h-screen bg-paper text-stone-900 antialiased dark:bg-ink dark:text-stone-100">
        <div class="grain" aria-hidden="true"></div>

        <a href="#main-content"
           class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-amber-600 focus:px-3 focus:py-1.5 focus:text-sm focus:font-medium focus:text-white">
            <?php echo e(__('nav.skip_to_content')); ?>

        </a>

        <header class="sticky top-0 z-10 border-b border-stone-200/80 bg-paper/85 backdrop-blur dark:border-stone-800/80 dark:bg-ink/85"
                x-data="{ menu: false }" x-on:keydown.escape.window="menu = false">
            <nav class="mx-auto flex h-14 max-w-6xl items-center justify-between gap-3 px-4">
                <div class="flex items-center gap-6">
                    <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2">
                        <svg class="h-5 w-5 -rotate-6 text-amber-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M3.5 2.5A1.5 1.5 0 0 1 5 1h10a1.5 1.5 0 0 1 1.5 1.5v11L12 17H5a1.5 1.5 0 0 1-1.5-1.5v-13Z"/>
                            <path d="M16.5 12H13a1 1 0 0 0-1 1v3.5h3.5a1 1 0 0 0 1-1V12Z" fill-opacity=".45"/>
                        </svg>
                        <span class="font-display text-lg font-semibold tracking-tight text-amber-700 dark:text-amber-400">
                            GrowStick
                        </span>
                    </a>
                    <div class="hidden items-center gap-4 text-sm sm:flex">
                        <a href="<?php echo e(route('home')); ?>"
                           class="<?php echo e(request()->routeIs('home') ? 'font-medium text-amber-700 dark:text-amber-400' : 'text-stone-600 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100'); ?>">
                            <?php echo e(__('nav.board')); ?>

                        </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('notes.wall')); ?>"
                               class="<?php echo e(request()->routeIs('notes.*') ? 'font-medium text-amber-700 dark:text-amber-400' : 'text-stone-600 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100'); ?>">
                                <?php echo e(__('nav.notes')); ?>

                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="hidden items-center gap-3 text-sm sm:flex">
                    <div class="flex items-center gap-0.5 rounded-full border border-stone-300 p-0.5 dark:border-stone-700">
                        <a href="<?php echo e(route('locale.switch', 'id')); ?>"
                           class="rounded-full px-2 py-0.5 text-xs font-medium transition <?php echo e(app()->getLocale() === 'id' ? 'bg-amber-600 text-white' : 'text-stone-500 hover:text-stone-900 dark:hover:text-stone-100'); ?>">
                            ID
                        </a>
                        <a href="<?php echo e(route('locale.switch', 'en')); ?>"
                           class="rounded-full px-2 py-0.5 text-xs font-medium transition <?php echo e(app()->getLocale() === 'en' ? 'bg-amber-600 text-white' : 'text-stone-500 hover:text-stone-900 dark:hover:text-stone-100'); ?>">
                            EN
                        </a>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <span class="hidden text-stone-600 dark:text-stone-400 md:inline"><?php echo e(auth()->user()->name); ?></span>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="rounded-lg border border-stone-300 bg-white px-3.5 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800">
                                <?php echo e(__('nav.logout')); ?>

                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="rounded-lg border border-stone-300 bg-white px-3.5 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800">
                            <?php echo e(__('nav.login')); ?>

                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="rounded-lg bg-amber-600 px-3.5 py-1.5 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.98]">
                            <?php echo e(__('nav.register')); ?>

                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <button type="button" x-on:click="menu = ! menu" x-bind:aria-expanded="menu"
                        aria-label="<?php echo e(__('nav.menu')); ?>"
                        class="rounded-lg border border-stone-300 bg-white p-1.5 text-stone-700 transition hover:bg-stone-100 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800 sm:hidden">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M3 5.75A.75.75 0 0 1 3.75 5h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 5.75Zm0 8.5a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"/>
                    </svg>
                </button>
            </nav>

            <div x-show="menu" x-cloak x-transition.opacity.duration.150ms
                 class="border-t border-stone-200/80 dark:border-stone-800/80 sm:hidden">
                <div class="space-y-1 px-4 py-3 text-sm">
                    <a href="<?php echo e(route('home')); ?>"
                       class="block rounded-lg px-3 py-2 font-medium <?php echo e(request()->routeIs('home') ? 'bg-amber-50 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'text-stone-700 hover:bg-stone-100 dark:text-stone-300 dark:hover:bg-stone-900'); ?>">
                        <?php echo e(__('nav.board')); ?>

                    </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('notes.wall')); ?>"
                           class="block rounded-lg px-3 py-2 font-medium <?php echo e(request()->routeIs('notes.*') ? 'bg-amber-50 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'text-stone-700 hover:bg-stone-100 dark:text-stone-300 dark:hover:bg-stone-900'); ?>">
                            <?php echo e(__('nav.notes')); ?>

                        </a>
                        <span class="block px-3 py-1 text-xs text-stone-500"><?php echo e(auth()->user()->name); ?></span>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full rounded-lg px-3 py-2 text-left font-medium text-stone-700 hover:bg-stone-100 dark:text-stone-300 dark:hover:bg-stone-900">
                                <?php echo e(__('nav.logout')); ?>

                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>"
                           class="block rounded-lg px-3 py-2 font-medium text-stone-700 hover:bg-stone-100 dark:text-stone-300 dark:hover:bg-stone-900">
                            <?php echo e(__('nav.login')); ?>

                        </a>
                        <a href="<?php echo e(route('register')); ?>"
                           class="block rounded-lg bg-amber-600 px-3 py-2 text-center font-medium text-white hover:bg-amber-700">
                            <?php echo e(__('nav.register')); ?>

                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="flex items-center gap-0.5 px-3 pt-2">
                        <a href="<?php echo e(route('locale.switch', 'id')); ?>"
                           class="rounded-full px-2 py-0.5 text-xs font-medium <?php echo e(app()->getLocale() === 'id' ? 'bg-amber-600 text-white' : 'text-stone-500'); ?>">ID</a>
                        <a href="<?php echo e(route('locale.switch', 'en')); ?>"
                           class="rounded-full px-2 py-0.5 text-xs font-medium <?php echo e(app()->getLocale() === 'en' ? 'bg-amber-600 text-white' : 'text-stone-500'); ?>">EN</a>
                    </div>
                </div>
            </div>
        </header>

        <main id="main-content" class="mx-auto max-w-6xl px-4 py-8">
            <?php echo e($slot); ?>

        </main>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    </body>
</html>
<?php /**PATH C:\Users\PIBI\OneDrive\Documents\MassPlanner\resources\views/layouts/app.blade.php ENDPATH**/ ?>