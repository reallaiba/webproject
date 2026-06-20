<?php $__env->startSection('title', 'Admin Projects'); ?>
<?php $__env->startSection('content'); ?>
<h2>Projects</h2>
<div class="table-container"><table><thead><tr><th>Title</th><th>Client</th><th>Status</th><th>Budget</th><th>Bids</th><th>Hired</th><th></th></tr></thead><tbody>
<?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($project->title); ?></td><td><?php echo e($project->client->name); ?></td><td><span class="badge badge-<?php echo e($project->status); ?>"><?php echo e(str_replace('_', ' ', $project->status)); ?></span></td><td>$<?php echo e(number_format($project->budget, 2)); ?></td><td><?php echo e($project->bids_count); ?></td><td><?php echo e($project->hiredFreelancer?->name ?? '-'); ?></td><td><a class="btn btn-sm btn-primary" href="<?php echo e(route('projects.show', $project)); ?>">View</a></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody></table></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\freelance_marketplace_laravel\resources\views/admin/projects.blade.php ENDPATH**/ ?>