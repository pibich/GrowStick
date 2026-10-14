<?php
use App\Models\Note;
use App\Services\Notes\NoteSnapshotWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
?>

<div class="space-y-6">
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm dark:border-stone-800 dark:bg-stone-900 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight"><?php echo e(__('notes.title')); ?></h1>
                <p class="mt-1 text-sm text-stone-600 dark:text-stone-400"><?php echo e(__('notes.subtitle')); ?></p>
            </div>
            <button type="button" wire:click="startCreate"
                    class="rounded-lg bg-amber-600 px-3.5 py-1.5 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.98]">
                + <?php echo e(__('notes.new')); ?>

            </button>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
            <div class="mt-4 rounded-lg bg-emerald-50 px-4 py-2 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($creating): ?>
        <div class="rounded-2xl border p-5 shadow-sm sm:p-6 <?php echo e(Note::colorClass($color)); ?>">
            <h2 class="font-semibold text-stone-900"><?php echo e(__('notes.new_title')); ?></h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="note-title" class="mb-1 block text-sm font-medium text-stone-800"><?php echo e(__('notes.field_title')); ?></label>
                    <input id="note-title" type="text" wire:model="title"
                           class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs font-medium text-red-700"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label for="note-body" class="mb-1 block text-sm font-medium text-stone-800"><?php echo e(__('notes.field_body')); ?></label>
                    <textarea id="note-body" rows="5" wire:model="body"
                              class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400"></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs font-medium text-red-700"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="grid gap-4 sm:flex sm:flex-wrap sm:items-end">
                    <div>
                        <span class="mb-1 block text-sm font-medium text-stone-800"><?php echo e(__('notes.field_color')); ?></span>
                        <div class="flex gap-2.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = Note::COLORS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paletteColor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <button type="button" wire:click="$set('color', '<?php echo e($paletteColor); ?>')"
                                        title="<?php echo e(__('notes.color.'.$paletteColor)); ?>"
                                        aria-label="<?php echo e(__('notes.color.'.$paletteColor)); ?>"
                                        aria-pressed="<?php echo e($color === $paletteColor ? 'true' : 'false'); ?>"
                                        class="h-8 w-8 rounded-full border-2 <?php echo e(Note::colorClass($paletteColor)); ?> <?php echo e($color === $paletteColor ? 'outline outline-2 outline-offset-2 outline-stone-900 dark:outline-stone-900' : ''); ?>"></button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs font-medium text-red-700"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="sm:min-w-52">
                        <label for="note-visibility" class="mb-1 block text-sm font-medium text-stone-800"><?php echo e(__('notes.field_visibility')); ?></label>
                        <select id="note-visibility" wire:model="visibility"
                                class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400">
                            <option value="private"><?php echo e(__('notes.visibility.private')); ?></option>
                            <option value="unlisted"><?php echo e(__('notes.visibility.unlisted')); ?></option>
                            <option value="public"><?php echo e(__('notes.visibility.public')); ?></option>
                        </select>
                        <p class="mt-1 text-xs text-stone-600"><?php echo e(__('notes.visibility_hint.'.$visibility)); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['visibility'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs font-medium text-red-700"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="flex gap-2 sm:ml-auto">
                        <button type="button" wire:click="create" wire:loading.attr="disabled" wire:loading.class="opacity-70"
                                class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.98] disabled:cursor-not-allowed">
                            <?php echo e(__('notes.save')); ?>

                        </button>
                        <button type="button" wire:click="cancelCreate"
                                class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-400 dark:bg-white dark:text-stone-800 dark:hover:bg-stone-100">
                            <?php echo e(__('notes.cancel')); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notes === []): ?>
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white/50 px-6 py-12 text-center dark:border-stone-700 dark:bg-stone-900/40">
            <svg class="mx-auto h-8 w-8 text-stone-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M3.5 2.5A1.5 1.5 0 0 1 5 1h10a1.5 1.5 0 0 1 1.5 1.5v11L12 17H5a1.5 1.5 0 0 1-1.5-1.5v-13Z"/>
                <path d="M16.5 12H13a1 1 0 0 0-1 1v3.5h3.5a1 1 0 0 0 1-1V12Z" fill-opacity=".4"/>
            </svg>
            <p class="mt-3 text-sm text-stone-500 dark:text-stone-400"><?php echo e(__('notes.empty')); ?></p>
        </div>
    <?php else: ?>
        <div class="grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'note-'.e($note['id']).''; ?>wire:key="note-<?php echo e($note['id']); ?>"
                     class="group relative rounded-xl border p-5 text-stone-800 shadow-sm transition hover:-rotate-1 hover:shadow-md active:rotate-0 <?php echo e(Note::colorClass($note['color'])); ?>">
                    <a href="<?php echo e(route('notes.show', $note['slug'])); ?>" class="block">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($note['title']): ?>
                            <h3 class="font-semibold"><?php echo e($note['title']); ?></h3>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <p class="mt-1 line-clamp-4 text-sm text-stone-700"><?php echo e($note['excerpt']); ?></p>
                    </a>

                    <div class="mt-3 flex items-center justify-between text-xs text-stone-600">
                        <span class="rounded-full bg-white/70 px-2 py-0.5"><?php echo e(__('notes.visibility.'.$note['visibility'])); ?></span>
                        <span><?php echo e($note['updated_at']); ?></span>
                    </div>

                    <button type="button" x-data
                            x-on:click="confirm('<?php echo e(__('notes.confirm_delete')); ?>') && $wire.call('delete', <?php echo e($note['id']); ?>)"
                            aria-label="<?php echo e(__('notes.delete')); ?>"
                            class="absolute right-2 top-2 rounded-lg border border-red-200 bg-white/90 px-2 py-1 text-xs font-medium text-red-600 transition hover:bg-red-50 sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
                        <?php echo e(__('notes.delete')); ?>

                    </button>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\Users\PIBI\OneDrive\Documents\MassPlanner\storage\framework\views/livewire/views/8ab52e26.blade.php ENDPATH**/ ?>