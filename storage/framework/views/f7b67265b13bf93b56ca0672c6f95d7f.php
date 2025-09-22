<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masterfile Show - Refactored</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .hairline {
            box-shadow: inset 0 0 0 1px #eaeaea;
        }
        .small-caps {
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .tabular {
            font-variant-numeric: tabular-nums;
        }
        .font-serif {
            font-family: Georgia, Cambria, "Times New Roman", Times, serif;
        }
        .font-sans {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
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
    <div class="w-screen min-h-screen bg-[#F7F7F9]"
         x-data="{
             edit: false,
             saving: false,
             originalFormData: null,

             initForm() {
                 this.originalFormData = new FormData(document.getElementById('mfForm'));
             },

             toggleEdit() {
                 if (!this.edit) {
                     this.initForm();
                 }
                 this.edit = !this.edit;
             },

             async saveForm() {
                 this.saving = true;
                 const form = document.getElementById('mfForm');
                 try {
                     const response = await fetch(form.action, {
                         method: 'POST',
                         body: new FormData(form),
                         headers: {
                             'X-Requested-With': 'XMLHttpRequest'
                         }
                     });
                     if (response.ok) {
                         this.edit = false;
                         this.showToast('Changes saved successfully');
                     } else {
                         throw new Error('Save failed');
                     }
                 } catch (error) {
                     this.showToast('Error saving changes', 'error');
                 } finally {
                     this.saving = false;
                 }
             },

             cancelEdit() {
                 if (this.edit) {
                     document.getElementById('mfForm').reset();
                     // Restore original values
                     if (this.originalFormData) {
                         for (let [key, value] of this.originalFormData.entries()) {
                             const input = document.querySelector(`[name='${key}']`);
                             if (input) input.value = value;
                         }
                     }
                     this.edit = false;
                 } else {
                     history.back();
                 }
             },

             showToast(message, type = 'success') {
                 // Simple toast implementation
                 const toast = document.createElement('div');
                 toast.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg z-50 ${
                     type === 'error' ? 'bg-[#d33831] text-white' : 'bg-[#22255b] text-white'
                 }`;
                 toast.textContent = message;
                 document.body.appendChild(toast);
                 setTimeout(() => toast.remove(), 3000);
             }
         }"
         x-init="initForm()">

        <!-- Sticky Toolbar -->
        <div class="sticky top-0 z-40 bg-white/80 backdrop-blur-sm border-b border-neutral-200/70">
            <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-4">
                <div class="flex items-center justify-between">
                    <!-- Left: Back Button -->
                    <div>
                        <a href="<?php echo e(route('dashboard')); ?>"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-neutral-600 bg-white rounded-xl border border-neutral-300 hover:bg-neutral-50 transition-colors duration-150"
                           title="Back to Dashboard">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                    
                    <form action="<?php echo e(route('masterfile.destroy', $file->id)); ?>"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this record? This cannot be undone.');"
                        class="mt-2 flex justify-end">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-red-600 text-black text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Delete
                        </button>
                    </form>

                    <!-- Center: Entity Title & Meta -->
                    <div class="text-center">
                        <h1 class="text-2xl font-serif text-[#1C1E26] font-semibold">
                            <?php echo e($file->company ?: ''); ?>

                        </h1>
                        <p class="text-sm text-neutral-500 mt-1">
                            ID: #<?php echo e($file->id); ?> • Created: <?php echo e($file->created_at ? $file->created_at->format('M d, Y') : ''); ?>

                        </p>
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="flex items-center gap-3">
                        <!-- TODO: Update route name if different -->
                        <a href="<?php echo e(route('masterfile.print', ['file' => $file->id])); ?>"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-neutral-700 bg-white rounded-xl border border-neutral-300 hover:bg-neutral-50 transition-colors duration-150"
                           title="Download Job Order">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Job Order
                        </a>

                        <button type="button"
                                @click="toggleEdit()"
                                x-show="!edit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#22255b] bg-white rounded-xl border border-[#22255b] hover:bg-[#22255b] hover:text-white transition-colors duration-150"
                                title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>

                        <button type="button"
                                @click="saveForm()"
                                x-show="edit"
                                x-style.display="edit ? 'flex' : 'none'"
                                :disabled="saving"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#22255b] rounded-xl hover:bg-[#1a1d4a] transition-colors duration-150 disabled:opacity-50"
                                title="Save Changes">
                            <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="saving ? 'Saving...' : 'Save'"></span>
                        </button>

                        <button type="button"
                                @click="cancelEdit()"
                                x-show="edit"
                                x-style.display="edit ? 'flex' : 'none'"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-neutral-700 bg-white rounded-xl border border-neutral-300 hover:bg-neutral-50 transition-colors duration-150"
                                title="Cancel">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-8">

            <form id="mfForm" method="POST" action="<?php echo e(route('masterfile.update', $file->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- Status Section -->
                <div class="mb-8">
                    <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-serif text-[#1C1E26] mb-2">Status & Product</h2>
                                <div class="flex items-center gap-6">
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm font-medium text-neutral-500">Status:</label>
                                        <select name="status"
                                                :readonly="!edit"
                                                :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'bg-transparent border-0 focus:ring-0'"
                                                class="px-3 py-1 text-sm font-medium rounded-full">
                                            <?php $__currentLoopData = ['pending','ongoing','completed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($s); ?>" <?php if($file->status === $s): echo 'selected'; endif; ?>>
                                                    <?php echo e(ucfirst($s)); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-sm font-medium text-neutral-500">Product:</label>
                                        <input name="product"
                                               value="<?php echo e(old('product', $file->product) ?: ''); ?>"
                                               :readonly="!edit"
                                               :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'bg-transparent border-0 focus:ring-0 text-[#4bbbed]'"
                                               class="px-3 py-1 text-sm font-medium">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Three Information Columns -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">

                    <!-- Project Information -->
                    <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50/50 to-indigo-50/50 border-b border-neutral-200/70">
                            <h3 class="text-sm font-medium text-neutral-600 small-caps tracking-wide">Project Information</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Month</label>
                                <input name="month"
                                       value="<?php echo e(old('month', $file->month) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Traffic</label>
                                <input name="traffic"
                                       value="<?php echo e(old('traffic', $file->traffic) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Current Location</label>
                                <input name="location"
                                       value="<?php echo e(old('location', $file->location) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Remarks</label>
                                <input name="duration"
                                       value="<?php echo e(old('duration', $file->duration) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Artwork</label>
                                <input name="artwork"
                                       value="<?php echo e(old('artwork', $file->artwork) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Person In Charge & Job Details -->
                    <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50/50 to-emerald-50/50 border-b border-neutral-200/70">
                            <h3 class="text-sm font-medium text-neutral-600 small-caps tracking-wide">Person In Charge & Job Details</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Client</label>
                                <input name="client"
                                       value="<?php echo e(old('client', $file->client) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Sales Person</label>
                                <input name="sales_person"
                                       value="<?php echo e(old('sales_person', $file->sales_person) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Email</label>
                                <input name="email"
                                       type="email"
                                       value="<?php echo e(old('email', $file->email) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Contact Number</label>
                                <input name="contact_number"
                                       value="<?php echo e(old('contact_number', $file->contact_number) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Job Number</label>
                                <input name="job_number"
                                       value="<?php echo e(old('job_number', $file->job_number) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Start Date</label>
                                <input name="date"
                                       type="date"
                                       value="<?php echo e(old('date', $file->date ? \Illuminate\Support\Str::of($file->date)->substr(0,10) : '')); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Date Finish</label>
                                <input name="date_finish"
                                       type="date"
                                       value="<?php echo e(old('date_finish', $file->date_finish ? \Illuminate\Support\Str::of($file->date_finish)->substr(0,10) : '')); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Information -->
                    <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50/50 to-pink-50/50 border-b border-neutral-200/70">
                            <h3 class="text-sm font-medium text-neutral-600 small-caps tracking-wide">Invoice Information</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Invoice Date</label>
                                <input name="invoice_date"
                                       type="date"
                                       value="<?php echo e(old('invoice_date', $file->invoice_date ? \Illuminate\Support\Str::of($file->invoice_date)->substr(0,10) : '')); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-500 mb-2">Invoice Number</label>
                                <input name="invoice_number"
                                       value="<?php echo e(old('invoice_number', $file->invoice_number) ?: ''); ?>"
                                       :readonly="!edit"
                                       :class="edit ? 'h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent' : 'hairline bg-neutral-50/50'"
                                       class="w-full px-4 py-3 text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Outdoor Placements Table -->
            <?php if($file->product_category === 'Outdoor' || $file->outdoorItems->count() > 0): ?>
            <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-50/50 to-green-50/50 border-b border-neutral-200/70">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-neutral-600 small-caps tracking-wide">Outdoor Placements</h3>
                        <span class="text-sm text-neutral-500">
                            Total locations: <span class="font-semibold tabular"><?php echo e($file->outdoorItems->count()); ?></span>
                            <?php $totalQty = $file->outdoorItems->sum('qty'); ?>
                            <?php if($totalQty !== $file->outdoorItems->count()): ?>
                                • Total qty: <span class="font-semibold tabular"><?php echo e($totalQty); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <?php if($file->outdoorItems->isEmpty()): ?>
                        <div class="p-8 text-center text-neutral-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>No outdoor placements added yet.</p>
                        </div>
                    <?php else: ?>
                        <table class="w-full text-sm">
                            <thead class="bg-emerald-50/50">
                                <tr class="text-left border-b border-neutral-200">
                                    <th class="px-4 py-3 font-medium text-neutral-600">#</th>
                                    <th class="px-4 py-3 font-medium text-neutral-600">Sub Product</th>
                                    <th class="px-4 py-3 font-medium text-neutral-600">Site / Location</th>
                                    <th class="px-4 py-3 font-medium text-neutral-600">Size</th>
                                    <th class="px-4 py-3 font-medium text-neutral-600">Start</th>
                                    <th class="px-4 py-3 font-medium text-neutral-600">End</th>
                                    <th class="px-4 py-3 font-medium text-neutral-600">Area</th>
                                    <th class="px-4 py-3 font-medium text-neutral-600">Coordinates</th>
                                    <th class="px-4 py-3 font-medium text-neutral-600 text-right">Qty</th>
                                    <th class="px-4 py-3 font-medium text-neutral-600">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <?php $__currentLoopData = $file->outdoorItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-neutral-50/50 transition-colors duration-150">
                                        <td class="px-4 py-3 text-neutral-600 tabular"><?php echo e($i + 1); ?></td>
                                        <td class="px-4 py-3 text-neutral-900"><?php echo e($item->sub_product ?: ''); ?></td>
                                        <td class="px-4 py-3 text-neutral-900"><?php echo e($item->site ?: ''); ?></td>
                                        <td class="px-4 py-3 text-neutral-900"><?php echo e($item->size ?: ''); ?></td>
                                        <td class="px-4 py-3 text-neutral-900">
                                            <?php echo e($item->start_date?->format('d/m/Y') ?? ''); ?>

                                        </td>
                                        <td class="px-4 py-3 text-neutral-900">
                                            <?php echo e($item->end_date?->format('d/m/Y') ?? ''); ?>

                                        </td>
                                        <td class="px-4 py-3 text-neutral-900"><?php echo e($item->district_council ?: ''); ?></td>
                                        <td class="px-4 py-3">
                                            <?php if($item->coordinates): ?>
                                                <a href="https://maps.google.com/?q=<?php echo e(urlencode($item->coordinates)); ?>"
                                                   target="_blank"
                                                   class="text-[#4bbbed] hover:underline focus:outline-none focus:ring-2 focus:ring-[#4bbbed] focus:ring-opacity-50 rounded">
                                                    <?php echo e($item->coordinates); ?>

                                                </a>
                                            <?php else: ?>
                                                <span class="text-neutral-400"></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-right tabular text-neutral-900"><?php echo e($item->qty ?? 1); ?></td>
                                        <td class="px-4 py-3 text-neutral-900"><?php echo e($item->remarks ?: ''); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
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
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/masterfile/show.blade.php ENDPATH**/ ?>