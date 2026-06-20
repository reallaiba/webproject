<?php $__env->startSection('title', 'FreelanceHub'); ?>
<?php $__env->startSection('content'); ?>
<section class="hero">
    <h1>FreelanceHub</h1>
    <p>Post projects, hire freelancers, sell gigs, and manage work in one marketplace.</p>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?php echo e(route('projects.index')); ?>">Browse Projects</a>
        <a class="btn btn-light" href="<?php echo e(route('gigs.index')); ?>">Browse Gigs</a>
    </div>
</section>
<div class="stats-grid">
    <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a class="stat-card stat-link-card" href="<?php echo e($stat['link']); ?>">
            <div class="stat-number"><?php echo e($stat['value']); ?></div>
            <div class="stat-label"><?php echo e($stat['label']); ?></div>
            <p><?php echo e($stat['help']); ?></p>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<section class="section">
    <h2>Categories</h2>
    <div class="cards">
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a class="card category-link-card" href="<?php echo e(route('projects.index', ['category' => $category->id])); ?>">
                <h3><?php echo e($category->icon); ?> <?php echo e($category->name); ?></h3>
                <p>Open projects in this category</p>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<section class="section">
    <h2>Active Freelancers</h2>
    <div class="cards">
        <?php $__currentLoopData = $freelancers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freelancer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card">
                <h3><?php echo e($freelancer->name); ?></h3>
                <p><?php echo e($freelancer->skills ?: 'Freelancer'); ?></p>
                <a class="btn btn-sm btn-outline-dark" href="<?php echo e(route('messages', ['with' => $freelancer->id])); ?>">Message</a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\freelance_marketplace_laravel\resources\views/home.blade.php ENDPATH**/ ?>