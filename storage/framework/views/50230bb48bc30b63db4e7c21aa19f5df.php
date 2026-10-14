<?php
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
?>

<div class="mx-auto max-w-md">
    <div class="rounded-2xl border border-t-4 border-stone-200 border-t-amber-400 bg-white p-6 shadow-sm dark:border-stone-800 dark:border-t-amber-500 dark:bg-stone-900">
        <h1 class="font-display text-xl font-semibold"><?php echo e(__('auth.register')); ?></h1>

        <form wire:submit="register" class="mt-4 space-y-4">
            <div>
                <label for="name" class="mb-1 block text-sm font-medium"><?php echo e(__('auth.name')); ?></label>
                <input id="name" type="text" wire:model="name" autocomplete="name"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:focus:ring-amber-900/50">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium"><?php echo e(__('auth.email')); ?></label>
                <input id="email" type="email" wire:model="email" autocomplete="email"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:focus:ring-amber-900/50">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium"><?php echo e(__('auth.password')); ?></label>
                <input id="password" type="password" wire:model="password" autocomplete="new-password"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:focus:ring-amber-900/50">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium"><?php echo e(__('auth.confirm_password')); ?></label>
                <input id="password_confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:focus:ring-amber-900/50">
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70"
                    class="w-full rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.99] disabled:cursor-not-allowed">
                <?php echo e(__('auth.register')); ?>

            </button>
        </form>

        <p class="mt-4 text-sm text-stone-600 dark:text-stone-400">
            <?php echo e(__('auth.already_have_account')); ?>

            <a href="<?php echo e(route('login')); ?>" class="font-medium text-amber-600 hover:underline dark:text-amber-400"><?php echo e(__('auth.login')); ?></a>
        </p>
    </div>
</div><?php /**PATH C:\Users\PIBI\OneDrive\Documents\MassPlanner\storage\framework\views/livewire/views/1b0c9918.blade.php ENDPATH**/ ?>