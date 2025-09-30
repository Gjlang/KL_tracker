<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Master File</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .hairline { box-shadow: inset 0 0 0 1px #eaeaea; }
        .small-caps { letter-spacing: .06em; text-transform: uppercase; }
        .tabular { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="font-sans">

<?php if (isset($component)) { $__componentOriginal4619374cef299e94fd7263111d0abc69 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4619374cef299e94fd7263111d0abc69 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div x-data="{ saving: false }" class="w-screen min-h-screen bg-[#F7F7F9]">

        <!-- Sticky Top Toolbar -->
        <div class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-[#EAEAEA]">
            <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-4">
                <div class="flex items-center justify-between">
                    <!-- Left: Back -->
                    <a href="<?php echo e(route('dashboard')); ?>"
                       class="inline-flex items-center text-[#22255b] hover:text-[#4bbbed] text-sm font-medium transition-colors duration-200"
                       title="Back to Dashboard">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Dashboard
                    </a>

                    <!-- Center: Title -->
                    <div class="text-center">
                        <h1 class="font-serif text-xl text-[#1C1E26] font-medium">Add New Master File</h1>
                        <p class="text-xs text-neutral-500 small-caps">Create</p>
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-3">
                        <button type="button"
                                onclick="history.back()"
                                class="px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50 rounded-xl transition-colors duration-200"
                                title="Cancel">
                            Cancel
                        </button>
                        <button type="reset"
                                form="mfForm"
                                class="px-4 py-2 text-sm border border-neutral-200 hover:bg-neutral-50 rounded-xl transition-colors duration-200"
                                title="Reset form">
                            Reset
                        </button>
                        <button type="submit"
                                form="mfForm"
                                :disabled="saving"
                                class="px-6 py-2 bg-[#22255b] text-white text-sm rounded-xl hover:opacity-95 disabled:opacity-60 shadow-sm transition-all duration-200 flex items-center gap-2"
                                title="Save master file">
                            <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span x-text="saving ? 'Saving...' : 'Save'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-8 pb-32">

            <!-- Date Display -->
            <div class="mb-6" x-data="{ today: new Date().toLocaleDateString('en-MY', { weekday:'long', year:'numeric', month:'long', day:'numeric' }) }">
                <p class="text-sm text-neutral-500">
                    Today: <span x-text="today" class="text-neutral-700"></span>
                </p>
            </div>

            <form id="mfForm"
                  action="<?php echo e(route('masterfile.store')); ?>"
                  method="POST"
                  @submit="saving = true"
                  x-data="productPicker()"
                  class="space-y-8">
                <?php echo csrf_field(); ?>

                <!-- Basic Information -->
                <div class="rounded-2xl border border-neutral-200/70 shadow-sm bg-white p-6">
                    <h3 class="text-sm text-neutral-600 small-caps mb-6 font-medium">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="month" class="block text-sm font-medium text-[#1C1E26] mb-2">Month</label>
                            <input type="text"
                                   name="month"
                                   id="month"
                                   value="<?php echo e(old('month')); ?>"
                                   placeholder="e.g., July"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                   required>
                            <?php $__errorArgs = ['month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="company" class="block text-sm font-medium text-[#1C1E26] mb-2">Company</label>
                            <input type="text"
                                   name="company"
                                   id="company"
                                   value="<?php echo e(old('company')); ?>"
                                   placeholder="Company Name"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                   required>
                            <?php $__errorArgs = ['company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="client" class="block text-sm font-medium text-[#1C1E26] mb-2">Person In Charge</label>
                            <input type="text"
                                   name="client"
                                   id="client"
                                   value="<?php echo e(old('client')); ?>"
                                   placeholder="PIC Name"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                   required>
                            <?php $__errorArgs = ['client'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="sales_person" class="block text-sm font-medium text-[#1C1E26] mb-2">Sales Person</label>
                            <input type="text"
                                   name="sales_person"
                                   id="sales_person"
                                   value="<?php echo e(old('sales_person')); ?>"
                                   placeholder="e.g., Aisyah / Daniel"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            <?php $__errorArgs = ['sales_person'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="contact_number" class="block text-sm font-medium text-[#1C1E26] mb-2">Contact Number</label>
                            <input type="text"
                                   name="contact_number"
                                   id="contact_number"
                                   value="<?php echo e(old('contact_number')); ?>"
                                   placeholder="e.g., +60 12-3456789"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            <?php $__errorArgs = ['contact_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-[#1C1E26] mb-2">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="<?php echo e(old('email')); ?>"
                                   placeholder="e.g., example@email.com"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Product & Traffic Details -->
                <div class="rounded-2xl border border-neutral-200/70 shadow-sm bg-white p-6">
                    <h3 class="text-sm text-neutral-600 small-caps mb-6 font-medium">Product & Traffic Details</h3>

                    <input type="hidden" name="product_category" :value="selectedCategory">

                    <!-- Product Category Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#1C1E26] mb-3">Product Category</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button"
                                    @click="selectCategory('KLTG')"
                                    :class="selectedCategory === 'KLTG'
                                        ? 'bg-[#22255b] text-white'
                                        : 'border border-neutral-200 text-neutral-600 hover:bg-neutral-50'"
                                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 focus:ring-2 focus:ring-[#4bbbed]">
                                KLTG
                            </button>
                            <button type="button"
                                    @click="selectCategory('Social Media Management')"
                                    :class="selectedCategory === 'Social Media Management'
                                        ? 'bg-[#22255b] text-white'
                                        : 'border border-neutral-200 text-neutral-600 hover:bg-neutral-50'"
                                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 focus:ring-2 focus:ring-[#4bbbed]">
                                Social Media Management
                            </button>
                            <button type="button"
                                    @click="selectCategory('Outdoor')"
                                    :class="selectedCategory === 'Outdoor'
                                        ? 'bg-[#22255b] text-white'
                                        : 'border border-neutral-200 text-neutral-600 hover:bg-neutral-50'"
                                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 focus:ring-2 focus:ring-[#4bbbed]">
                                Outdoor
                            </button>
                        </div>
                    </div>

                    <!-- Product Selection -->
                    <div class="mb-6">
                        <label for="product" class="block text-sm font-medium text-[#1C1E26] mb-2">Product Type</label>
                        <select name="product"
                                x-model="selectedProduct"
                                @change="refreshNumbers()"
                                class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                required>
                            <option value="">Select a product...</option>
                            <template x-for="product in getCurrentProducts()" :key="product.value">
                                <option :value="product.value" x-text="product.label"></option>
                            </template>
                        </select>
                        <?php $__errorArgs = ['product'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Other Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="traffic" class="block text-sm font-medium text-[#1C1E26] mb-2">Traffic</label>
                            <input type="text"
                                   name="traffic"
                                   id="traffic"
                                   value="<?php echo e(old('traffic')); ?>"
                                   placeholder="Traffic Details"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                   required>
                            <?php $__errorArgs = ['traffic'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-[#1C1E26] mb-2">Amount (MYR)</label>
                            <input type="number"
                                   name="amount"
                                   id="amount"
                                   step="0.01"
                                   min="0"
                                   value="<?php echo e(old('amount')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200 tabular">
                            <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-[#1C1E26] mb-2">Duration</label>
                            <input type="text"
                                   name="duration"
                                   id="duration"
                                   value="<?php echo e(old('duration')); ?>"
                                   placeholder="e.g., 3 months"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                   required>
                            <?php $__errorArgs = ['duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-[#1C1E26] mb-2">Start Date</label>
                            <input type="date"
                                   name="date"
                                   id="date"
                                   value="<?php echo e(old('date')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                   required>
                            <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="date_finish" class="block text-sm font-medium text-[#1C1E26] mb-2">Date Finish</label>
                            <input type="date"
                                   name="date_finish"
                                   id="date_finish"
                                   value="<?php echo e(old('date_finish')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                   required>
                            <?php $__errorArgs = ['date_finish'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-[#1C1E26] mb-2">Status</label>
                            <select name="status"
                                    id="status"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                <option value="pending" <?php echo e(old('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="artwork" class="block text-sm font-medium text-[#1C1E26] mb-2">Artwork</label>
                            <select name="artwork"
                                    id="artwork"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                <option value="BGOC" <?php echo e(old('artwork') === 'BGOC' ? 'selected' : ''); ?>>BGOC</option>
                                <option value="Client" <?php echo e(old('artwork') === 'Client' ? 'selected' : ''); ?>>Client</option>
                            </select>
                            <?php $__errorArgs = ['artwork'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="job_number" class="block text-sm font-medium text-[#1C1E26] mb-2">Job Number</label>
                            <input type="text"
                                   name="job_number"
                                   id="job_number"
                                   value="<?php echo e(old('job_number')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl bg-neutral-50 tabular"
                                   readonly>
                        </div>

                        <div>
                            <label for="remarks" class="block text-sm font-medium text-[#1C1E26] mb-2">Remarks</label>
                            <input type="text"
                                   name="remarks"
                                   id="remarks"
                                   value="<?php echo e(old('remarks')); ?>"
                                   placeholder="Remarks"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                   required>
                            <?php $__errorArgs = ['remarks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-sm text-[#d33831]"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- KLTG Details -->
                <div x-show="selectedCategory === 'KLTG'"
                     x-cloak
                     class="rounded-2xl border border-neutral-200/70 shadow-sm bg-white p-6">
                    <h3 class="text-sm text-neutral-600 small-caps mb-6 font-medium">KLTG Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="kltg_industry" class="block text-sm font-medium text-[#1C1E26] mb-2">Industry</label>
                            <input type="text"
                                   name="kltg_industry"
                                   id="kltg_industry"
                                   value="<?php echo e(old('kltg_industry')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_x" class="block text-sm font-medium text-[#1C1E26] mb-2">X</label>
                            <input type="text"
                                   name="kltg_x"
                                   id="kltg_x"
                                   value="<?php echo e(old('kltg_x')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_edition" class="block text-sm font-medium text-[#1C1E26] mb-2">Edition</label>
                            <input type="text"
                                   name="kltg_edition"
                                   id="kltg_edition"
                                   value="<?php echo e(old('kltg_edition')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_material_cbp" class="block text-sm font-medium text-[#1C1E26] mb-2">Material C/BP</label>
                            <input type="text"
                                   name="kltg_material_cbp"
                                   id="kltg_material_cbp"
                                   value="<?php echo e(old('kltg_material_cbp')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_print" class="block text-sm font-medium text-[#1C1E26] mb-2">Print</label>
                            <input type="text"
                                   name="kltg_print"
                                   id="kltg_print"
                                   value="<?php echo e(old('kltg_print')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_article" class="block text-sm font-medium text-[#1C1E26] mb-2">Article</label>
                            <input type="text"
                                   name="kltg_article"
                                   id="kltg_article"
                                   value="<?php echo e(old('kltg_article')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_video" class="block text-sm font-medium text-[#1C1E26] mb-2">Video</label>
                            <input type="text"
                                   name="kltg_video"
                                   id="kltg_video"
                                   value="<?php echo e(old('kltg_video')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_leaderboard" class="block text-sm font-medium text-[#1C1E26] mb-2">Leaderboard</label>
                            <input type="text"
                                   name="kltg_leaderboard"
                                   id="kltg_leaderboard"
                                   value="<?php echo e(old('kltg_leaderboard')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_qr_code" class="block text-sm font-medium text-[#1C1E26] mb-2">QR Code</label>
                            <input type="text"
                                   name="kltg_qr_code"
                                   id="kltg_qr_code"
                                   value="<?php echo e(old('kltg_qr_code')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_blog" class="block text-sm font-medium text-[#1C1E26] mb-2">Blog</label>
                            <input type="text"
                                   name="kltg_blog"
                                   id="kltg_blog"
                                   value="<?php echo e(old('kltg_blog')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_em" class="block text-sm font-medium text-[#1C1E26] mb-2">EM</label>
                            <input type="text"
                                   name="kltg_em"
                                   id="kltg_em"
                                   value="<?php echo e(old('kltg_em')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="barter" class="block text-sm font-medium text-[#1C1E26] mb-2">Barter</label>
                            <input type="text"
                                   name="barter"
                                   id="barter"
                                   value="<?php echo e(old('barter')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>

                        <div>
                            <label for="kltg_remarks" class="block text-sm font-medium text-[#1C1E26] mb-2">Remarks (KLTG)</label>
                            <input type="text"
                                   name="kltg_remarks"
                                   id="kltg_remarks"
                                   value="<?php echo e(old('kltg_remarks')); ?>"
                                   class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                        </div>
                    </div>
                </div>


                <!-- Outdoor Details -->
            <div
            x-show="selectedCategory === 'Outdoor'"
            x-cloak
            class="rounded-2xl border border-neutral-200/70 shadow-sm bg-white p-6"
            x-data="outdoorRepeater(() => selectedProduct)"   <!-- pass getter so it always reads current sub-product -->

            >
            <h3 class="text-sm text-neutral-600 small-caps mb-6 font-medium">Outdoor Details</h3>

            <!-- Count selector -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-[#1C1E26] mb-2">How many locations?</label>
                <div class="flex items-center gap-3">
                <input type="number" min="1" x-model.number="count"
                        @input="resize()"  <!-- react immediately -->
                <button type="button" @click="addOne()"
                        class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">+ Add 1</button>
                <button type="button" @click="removeOne()"
                        class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">− Remove 1</button>
                </div>

                <button type="button" @click="copyDatesToAll()"
            class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">
            Copy dates from #1 to all
            </button>

            <button type="button" @click="copySizeToAll()"
            class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">
            Copy size from #1 to all
            </button>

            <button type="button" @click="copySubToAll()"
            class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">
            Copy sub-product from #1 to all
            </button>
                <p class="mt-2 text-xs text-neutral-500">You can also add/remove rows anytime.</p>
            </div>

            <!-- Repeater rows -->
            <template x-for="(row, idx) in rows" :key="idx">
                <div class="mb-4 p-4 rounded-xl border border-neutral-200/80 bg-neutral-50/40">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-sm font-medium text-[#1C1E26]">Location <span x-text="idx+1"></span></h4>
                    <button type="button" @click="removeAt(idx)"
                            class="text-xs px-2 py-1 rounded-lg border border-neutral-300 hover:bg-neutral-100">Remove</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                    <!-- Sub-product override (optional) -->
                    <div class="md:col-span-1">
                    <label class="block text-xs text-neutral-600 mb-1">Sub-Product</label>
                    <select :name="`locations[${idx}][sub_product]`" x-model="row.sub_product"
                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]">
                        <template x-for="opt in outdoorSubProducts" :key="opt">
                        <option :value="opt" x-text="opt"></option>
                        </template>
                    </select>
                    </div>

                    <div class="md:col-span-2">
                    <label class="block text-xs text-neutral-600 mb-1">Site</label>
                    <input type="text" :name="`locations[${idx}][site]`" x-model="row.site"
                            placeholder="e.g., Wangsa Maju LRT"
                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                    </div>

                    <div class="md:col-span-1">
                    <label class="block text-xs text-neutral-600 mb-1">Size</label>
                    <input type="text" :name="`locations[${idx}][size]`" x-model="row.size"
                            placeholder="10x20ft"
                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                    </div>

                    <div class="md:col-span-1">
                    <label class="block text-xs text-neutral-600 mb-1">Area</label>
                    <input type="text" :name="`locations[${idx}][council]`" x-model="row.council"
                            placeholder="AREA"
                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                    </div>

                    <div class="md:col-span-1">
                    <label class="block text-xs text-neutral-600 mb-1">Coords (lat,lng)</label>
                    <input type="text" :name="`locations[${idx}][coords]`" x-model="row.coords"
                            placeholder="3.154,101.74"
                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                    </div>

                    <div class="md:col-span-1">
                    <label class="block text-xs text-neutral-600 mb-1">Start Date</label>
                    <input type="date" :name="`locations[${idx}][start_date]`" x-model="row.start_date"
                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                    </div>

                    <div class="md:col-span-1">
                    <label class="block text-xs text-neutral-600 mb-1">End Date</label>
                    <input type="date" :name="`locations[${idx}][end_date]`" x-model="row.end_date"
                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                    </div>

                    <div class="md:col-span-6">
                    <label class="block text-xs text-neutral-600 mb-1">Remarks</label>
                    <input type="text" :name="`locations[${idx}][remarks]`" x-model="row.remarks"
                            placeholder="Near station"
                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                    </div>
                </div>
                </div>
            </template>

            <input type="hidden" name="input_mode" value="structured" />
            </div>

            <script>
            window.outdoorRepeater = function(selectedProductRef) {
                return {
                outdoorSubProducts: ['BB','TB','Newspaper','Bunting','Flyers','Star','Signages'],

                count: 1,
                rows: [],

                // sync toggles
                syncDates: false,
                syncSize:  false,     // NEW
                syncSub:   false,     // NEW

                init() {
                    const def = (typeof selectedProductRef === 'function')
                    ? selectedProductRef() : (selectedProductRef || 'BB');
                    this.rows = [this.emptyRow(def)];
                    this.count = this.rows.length;

                    // === keep others synced when toggled ===
                    // dates
                    this.$watch('rows[0].start_date', v => { if (this.syncDates) this.rows.forEach((r,i)=>{ if(i){ r.start_date=v } }); });
                    this.$watch('rows[0].end_date',   v => { if (this.syncDates) this.rows.forEach((r,i)=>{ if(i){ r.end_date=v } }); });

                    // size
                    this.$watch('rows[0].size',       v => { if (this.syncSize)  this.rows.forEach((r,i)=>{ if(i){ r.size=v } }); });

                    // sub-product
                    this.$watch('rows[0].sub_product', v => {
                    if (!this.syncSub) return;
                    // only set if value is in allowed list
                    if (!this.outdoorSubProducts.includes(v)) return;
                    this.rows.forEach((r,i)=>{ if(i){ r.sub_product=v } });
                    });
                },

                emptyRow(defaultSub) {
                    return {
                    sub_product: defaultSub || 'BB',
                    site: '',
                    size: '',
                    council: '',
                    coords: '',
                    remarks: '',
                    start_date: '',
                    end_date: ''
                    };
                },

                // === one-click copy actions ===
                copyDatesToAll() {
                    const s = this.rows[0]?.start_date || '';
                    const e = this.rows[0]?.end_date   || '';
                    this.rows.forEach((r,i)=>{ if(i){ r.start_date=s; r.end_date=e; } });
                },
                copySizeToAll() {                              // NEW
                    const v = this.rows[0]?.size || '';
                    this.rows.forEach((r,i)=>{ if(i){ r.size=v; } });
                },
                copySubToAll() {                               // NEW
                    const v = this.rows[0]?.sub_product || '';
                    if (!this.outdoorSubProducts.includes(v)) return;
                    this.rows.forEach((r,i)=>{ if(i){ r.sub_product=v; } });
                },

                // === row management ===
                resize() {
                    const def = this.rows[0]?.sub_product || 'BB';
                    const target = Math.max(1, parseInt(this.count || 1, 10));
                    while (this.rows.length < target) {
                    const r = this.emptyRow(def);
                    // inherit synced fields for new rows
                    if (this.syncDates) { r.start_date = this.rows[0]?.start_date || ''; r.end_date = this.rows[0]?.end_date || ''; }
                    if (this.syncSize)  { r.size       = this.rows[0]?.size       || ''; }
                    if (this.syncSub)   { r.sub_product= this.rows[0]?.sub_product|| def; }
                    this.rows.push(r);
                    }
                    while (this.rows.length > target) this.rows.pop();
                },

                addOne() {
                    const last = this.rows[this.rows.length - 1];
                    const def  = last?.sub_product || 'BB';
                    const r    = this.emptyRow(def);
                    // inherit synced fields for the new row
                    if (this.syncDates) { r.start_date = this.rows[0]?.start_date || ''; r.end_date = this.rows[0]?.end_date || ''; }
                    if (this.syncSize)  { r.size       = this.rows[0]?.size       || ''; }
                    if (this.syncSub)   { r.sub_product= this.rows[0]?.sub_product|| def; }
                    this.rows.push(r);
                    this.count = this.rows.length;
                },

                removeOne() {
                    if (this.rows.length > 1) { this.rows.pop(); this.count = this.rows.length; }
                },

                removeAt(i) {
                    if (this.rows.length > 1) { this.rows.splice(i,1); this.count = this.rows.length; }
                }
                }
            }
            </script>


            <style>[x-cloak]{display:none !important;}</style>
        <!-- Sticky Bottom Action Bar -->
        <div class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-t border-[#EAEAEA]">
            <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-4">
                <div class="flex justify-end items-center gap-3">
                    <button type="button"
                            onclick="history.back()"
                            class="px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50 rounded-xl transition-colors duration-200"
                            title="Cancel">
                        Cancel
                    </button>
                    <button type="reset"
                            form="mfForm"
                            class="px-4 py-2 text-sm border border-neutral-200 hover:bg-neutral-50 rounded-xl transition-colors duration-200"
                            title="Reset form">
                        Reset
                    </button>
                    <button type="submit"
                            form="mfForm"
                            :disabled="saving"
                            class="px-6 py-2 bg-[#22255b] text-white text-sm rounded-xl hover:opacity-95 disabled:opacity-60 shadow-sm transition-all duration-200 flex items-center gap-2"
                            title="Save master file">
                        <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span x-text="saving ? 'Saving...' : 'Save'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function productPicker() {
      return {
        // ===== Data =====
        categories: {
          'KLTG': [
            { label: 'THE GUIDE',                value: 'THE GUIDE' },
            { label: 'KLTG listing',        value: 'KLTG listing' },
            { label: 'KLTG Quarter Page',   value: 'KLTG Quarter Page' },
          ],
          'Social Media Management': [
            { label: 'TikTok Management',           value: 'TikTok Management' },
            { label: 'YouTube Management',          value: 'YouTube Management' },
            { label: 'FB/IG Management',            value: 'FB/IG Management' },
            { label: 'FB Sponsored Ads',            value: 'FB Sponsored Ads' },  // was "FB IG Ad"
            { label: 'TikTok Management Boost',     value: 'TikTok Management Boost' },
            { label: 'Giveaways/ Contest Management', value: 'Giveaways/ Contest Management' },
            { label: 'Xiaohongshu Management',      value: 'Xiaohongshu Management' },
          ],
          'Outdoor': [
            { label: 'TB - Tempboard',  value: 'TB' },
            { label: 'BB - Billboard',  value: 'BB' },
            { label: 'Newspaper',       value: 'Newspaper' },
            { label: 'Bunting',         value: 'Bunting' },
            { label: 'Flyers',          value: 'Flyers' },
            { label: 'Star',            value: 'Star' },
            { label: 'Signages',        value: 'Signages' },
          ],
        },

        selectedCategory: '',
        selectedProduct: <?php echo json_encode(old('product', ''), 512) ?>,

        // ===== Lifecycle =====
        init() {
          // Detect category from old product or default to KLTG
          if (this.selectedProduct) this.detectCategoryFromProduct(this.selectedProduct);
          if (!this.selectedCategory) this.selectedCategory = 'KLTG';

          // Keep hidden input in sync
          this._syncHiddenCategory();

          // Expose refreshNumbers for legacy @change="refreshNumbers()"
          window.refreshNumbers = this.refreshNumbers.bind(this);

          // Auto-refresh when product/date changes
          const dateEl = document.querySelector('input[name="date"]');
          if (dateEl) dateEl.addEventListener('change', () => this.refreshNumbers());
          this.$watch('selectedProduct', () => this.refreshNumbers());

          // Initial attempt
          this.refreshNumbers();
        },

        // ===== Actions =====
        selectCategory(category) {
          this.selectedCategory = category;
          // Clear product if it doesn't belong to new category
          if (!this.isProductInCategory(this.selectedProduct, category)) {
            this.selectedProduct = '';
          }
          this._syncHiddenCategory();
        },

        detectCategoryFromProduct(productValue) {
          for (const [category, products] of Object.entries(this.categories)) {
            if (products.some(p => p.value === productValue)) {
              this.selectedCategory = category;
              return;
            }
          }
        },

        isProductInCategory(productValue, category) {
          return !!(this.categories[category] && this.categories[category].some(p => p.value === productValue));
        },

        getCurrentProducts() {
          return this.categories[this.selectedCategory] || [];
        },

        _syncHiddenCategory() {
          const hidden = document.querySelector('input[name="product_category"]');
          if (hidden) hidden.value = this.selectedCategory;
        },

        // ===== Job number preview =====
        async refreshNumbers() {
          const dateEl = document.querySelector('input[name="date"]');
          const prodEl = document.querySelector('select[name="product"]');
          const jobEl  = document.getElementById('job_number');

          const date = dateEl?.value || '';
          const product = prodEl?.value || this.selectedProduct || '';

          if (!date || !product || !jobEl) return;

          try {
            // 
            const url = new URL('/serials/preview', window.location.origin);
            url.searchParams.set('date', date);
            url.searchParams.set('product', product);

            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return; // don't block the form if backend errs

            const data = await res.json();
            if (data?.job_number) jobEl.value = data.job_number;
          } catch (e) {
            // Silent fail to keep UX smooth
            console.warn('refreshNumbers failed:', e);
          }
        },
      };
    }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $attributes = $__attributesOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__attributesOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $component = $__componentOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__componentOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>

</body>
</html>
<?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views/masterfile/create.blade.php ENDPATH**/ ?>