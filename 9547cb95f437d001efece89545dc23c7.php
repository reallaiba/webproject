<?php $__env->startSection('title', 'Projects'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-head">
    <h2>Projects</h2>
    <?php if(auth()->guard()->check()): ?> <?php if(auth()->user()->isClient()): ?><a class="btn btn-primary" href="<?php echo e(route('projects.create')); ?>">Post Project</a><?php endif; ?> <?php endif; ?>
</div>
<form class="search-bar" method="get" action="<?php echo e(route('projects.index')); ?>">
    <select name="category" onchange="this.form.submit()">
        <option value="">All categories</option>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($category->id); ?>" <?php if(request('category') == $category->id): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select name="status" onchange="this.form.submit()">
        <option value="">All project status</option>
        <option value="open" <?php if(request('status') === 'open'): echo 'selected'; endif; ?>>Open</option>
        <option value="in_progress" <?php if(request('status') === 'in_progress'): echo 'selected'; endif; ?>>In Progress</option>
        <option value="completed" <?php if(request('status') === 'completed'): echo 'selected'; endif; ?>>Completed</option>
    </select>
    <?php if(request('category') || request('status')): ?>
        <a class="btn btn-sm btn-outline-dark" href="<?php echo e(route('projects.index')); ?>">Clear</a>
    <?php endif; ?>
</form>
<div class="cards">
    <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card">
            <h3><?php echo e($project->title); ?></h3>
            <p class="text-muted"><?php echo e($project->category?->name ?? 'General'); ?> - <?php echo e($project->client->name); ?></p>
            <p><?php echo e(Str::limit($project->description, 120)); ?></p>
            <p class="card-price">$<?php echo e(number_format($project->budget, 2)); ?></p>
            <p><span class="badge badge-<?php echo e($project->status); ?>"><?php echo e(str_replace('_', ' ', $project->status)); ?></span> <span class="text-muted"><?php echo e($project->bids_count); ?> bid<?php echo e($project->bids_count === 1 ? '' : 's'); ?></span></p>
            <a class="btn btn-sm btn-primary" href="<?php echo e(route('projects.show', $project)); ?>">View</a>
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->isFreelancer() && $project->status === 'open'): ?>
                    <a class="btn btn-sm btn-outline-dark" href="<?php echo e(route('bids.create', $project)); ?>">Bid</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="card empty-state-card">
            <h3>No projects found</h3>
            <p>Try another category or clear the filters to see available marketplace projects.</p>
            <a class="btn btn-sm btn-outline-dark" href="<?php echo e(route('projects.index')); ?>">Clear Filters</a>
        </div>
    <?php endif; ?>
</div>
<?php echo e($projects->links()); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\freelance_marketplace_laravel\resources\views/projects/index.blade.php ENDPATH**/ ?>