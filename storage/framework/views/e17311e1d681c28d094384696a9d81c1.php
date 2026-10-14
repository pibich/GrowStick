<?php
use App\Models\Note;
use App\Services\Notes\NoteSnapshotWriter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
?>

<div class="mx-auto max-w-3xl space-y-6" data-note-color="<?php echo e($color); ?>">
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm dark:border-stone-800 dark:bg-stone-900 sm:p-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight"><?php echo e($title !== '' ? $title : __('notes.untitled')); ?></h1>
                <p class="mt-1 text-sm text-stone-500">
                    <?php echo e(__('notes.by', ['user' => $userName ?? '—'])); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($updatedAt): ?>
                        · <?php echo e($updatedAt); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    · <span class="rounded-full bg-stone-100 px-2 py-0.5 dark:bg-stone-800"><?php echo e(__('notes.visibility.'.$visibility)); ?></span>
                </p>
            </div>
            <a href="<?php echo e(route('home')); ?>" class="shrink-0 text-sm text-stone-500 transition hover:text-stone-900 dark:hover:text-stone-100">
                <?php echo e(__('notes.back_board')); ?>

            </a>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $dbAvailable): ?>
                <span class="text-sm text-stone-400"><?php echo e(__('notes.db_unavailable')); ?></span>
            <?php elseif($isOwner): ?>
                <button type="button" x-data="{ copied: false }"
                        x-on:click="navigator.clipboard.writeText(window.location.href).then(() => { copied = true; setTimeout(() => copied = false, 1500); })"
                        class="rounded-lg border border-stone-300 bg-white px-3.5 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800">
                    <span x-show="!copied"><?php echo e(__('notes.copy_link')); ?></span>
                    <span x-show="copied" x-cloak class="text-emerald-600"><?php echo e(__('notes.link_copied')); ?></span>
                </button>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($visibility === 'private'): ?>
                    <a href="<?php echo e(route('notes.wall')); ?>"
                       class="rounded-lg border border-stone-300 bg-white px-3.5 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800">
                        <?php echo e(__('notes.back_wall')); ?>

                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php elseif(! Auth::check()): ?>
                <a href="<?php echo e(route('login')); ?>" class="text-sm font-medium text-amber-600 hover:underline dark:text-amber-400"><?php echo e(__('notes.login_hint')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
            <div class="mt-4 rounded-lg bg-emerald-50 px-4 py-2 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="rounded-2xl border p-5 text-stone-800 shadow-sm sm:p-6 <?php echo e(Note::colorClass($color)); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editing): ?>
            <div class="space-y-3">
                <input type="text" wire:model="editTitle" placeholder="<?php echo e(__('notes.field_title')); ?>"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-semibold text-stone-900 placeholder:font-normal placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editTitle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs font-medium text-red-700"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <textarea rows="10" wire:model="editBody" placeholder="<?php echo e(__('notes.field_body')); ?>"
                          class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400"></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['editBody'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs font-medium text-red-700"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <div class="flex gap-2.5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = Note::COLORS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paletteColor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <button type="button" wire:click="$set('editColor', '<?php echo e($paletteColor); ?>')"
                                    title="<?php echo e(__('notes.color.'.$paletteColor)); ?>"
                                    aria-label="<?php echo e(__('notes.color.'.$paletteColor)); ?>"
                                    aria-pressed="<?php echo e($editColor === $paletteColor ? 'true' : 'false'); ?>"
                                    class="h-7 w-7 rounded-full border-2 <?php echo e(Note::colorClass($paletteColor)); ?> <?php echo e($editColor === $paletteColor ? 'outline outline-2 outline-offset-2 outline-stone-900 dark:outline-stone-900' : ''); ?>"></button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    <div class="sm:min-w-52">
                        <select wire:model="editVisibility"
                                class="w-full rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-sm text-stone-900 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400">
                            <option value="private"><?php echo e(__('notes.visibility.private')); ?></option>
                            <option value="unlisted"><?php echo e(__('notes.visibility.unlisted')); ?></option>
                            <option value="public"><?php echo e(__('notes.visibility.public')); ?></option>
                        </select>
                        <p class="mt-1 text-xs text-stone-600"><?php echo e(__('notes.visibility_hint.'.$editVisibility)); ?></p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:ml-auto">
                        <button type="button" wire:click="save" wire:loading.attr="disabled" wire:loading.class="opacity-70"
                                class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.98] disabled:cursor-not-allowed">
                            <?php echo e(__('notes.save')); ?>

                        </button>
                        <button type="button" wire:click="cancelEdit"
                                class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-400 dark:bg-white dark:text-stone-800 dark:hover:bg-stone-100">
                            <?php echo e(__('notes.cancel')); ?>

                        </button>
                        <button type="button" x-data
                                x-on:click="confirm('<?php echo e(__('notes.confirm_delete')); ?>') && $wire.call('delete')"
                                class="rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 active:scale-[.98] dark:border-red-300 dark:bg-white dark:hover:bg-red-50">
                            <?php echo e(__('notes.delete')); ?>

                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="whitespace-pre-wrap text-sm leading-6 text-stone-700"><?php echo e($body); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $editing && $isOwner): ?>
        <div class="text-center">
            <button type="button" wire:click="startEdit"
                    class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800">
                <?php echo e(__('notes.edit')); ?>

            </button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\PIBI\OneDrive\Documents\MassPlanner\storage\framework\views/livewire/views/30520500.blade.php ENDPATH**/ ?>