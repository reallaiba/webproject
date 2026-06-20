<?php $__env->startSection('title', 'Admin Gigs'); ?>
<?php $__env->startSection('content'); ?>
<h2>Gigs</h2>
<div class="table-container"><table><thead><tr><th>Title</th><th>Freelancer</th><th>Price</th><th>Status</th><th></th></tr></thead><tbody>
<?php $__currentLoopData = $gigs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($gig->title); ?></td><td><?php echo e($gig->freelancer->name); ?></td><td>$<?php echo e(number_format($gig->price, 2)); ?></td><td><?php echo e($gig->status); ?></td><td><form method="post" action="<?php echo e(route('admin.gigs.toggle', $gig)); ?>"><?php echo csrf_field(); ?> <button class="btn btn-sm btn-danger">Toggle</button></form></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody></table></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\freelance_marketplace_laravel\resources\views/admin/gigs.blade.php ENDPATH**/ ?>