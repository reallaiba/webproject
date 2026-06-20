<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div class="dashboard-welcome">
    <div><h2>Welcome, <?php echo e($user->name); ?></h2><p>Logged in as <span class="badge"><?php echo e($user->role); ?></span></p></div>
</div>
<div class="dashboard-actions">
    <?php if($user->isClient()): ?>
        <a class="btn btn-primary" href="<?php echo e(route('projects.create')); ?>">Post Project</a>
        <a class="btn btn-primary" href="<?php echo e(route('client.projects')); ?>">My Projects</a>
        <a class="btn btn-primary" href="<?php echo e(route('gigs.index')); ?>">Browse Gigs</a>
        <a class="btn btn-primary" href="<?php echo e(route('cart')); ?>">Cart</a>
    <?php endif; ?>
    <?php if($user->isFreelancer()): ?>
        <a class="btn btn-primary" href="<?php echo e(route('projects.index')); ?>">Browse Projects</a>
        <a class="btn btn-primary" href="<?php echo e(route('gigs.create')); ?>">Create Gig</a>
        <a class="btn btn-primary" href="<?php echo e(route('my.gigs')); ?>">My Gigs</a>
        <a class="btn btn-primary" href="<?php echo e(route('my.bids')); ?>">My Bids</a>
        <a class="btn btn-primary" href="<?php echo e(route('earnings')); ?>">Earnings</a>
    <?php endif; ?>
    <a class="btn btn-outline-dark" href="<?php echo e(route('messages')); ?>">Messages</a>
    <a class="btn btn-outline-dark" href="<?php echo e(route('profile')); ?>">Profile</a>
</div>
<section class="section">
    <h2>Recent Activity</h2>
    <div class="dashboard-grid">
        <div class="card">
            <h3><?php echo e($user->isClient() ? 'My Posted Projects & Bids' : 'My Submitted Bids'); ?></h3>
            <div class="compact-list">
                <?php $__currentLoopData = $clientProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('projects.show', $project)); ?>">
                        <strong><?php echo e($project->title); ?></strong>
                        <span>$<?php echo e(number_format($project->budget, 2)); ?> budget - <?php echo e($project->bids_count); ?> bid<?php echo e($project->bids_count === 1 ? '' : 's'); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $freelancerBids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('projects.show', $bid->project)); ?>">
                        <strong><?php echo e($bid->project->title); ?></strong>
                        <span>Your bid: $<?php echo e(number_format($bid->bid_amount, 2)); ?> - <?php echo e($bid->status); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($clientProjects->isEmpty() && $freelancerBids->isEmpty()): ?>
                    <p class="text-muted">No activity yet. Use the buttons above to start.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <h3>Messages From Database</h3>
            <div class="compact-list">
                <?php $__empty_1 = true; $__currentLoopData = $recentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('messages', ['with' => $message->sender_id])); ?>">
                        <strong><?php echo e($message->sender->name); ?></strong>
                        <span><?php echo e(Str::limit($message->message, 70)); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted">No messages yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <h3>Alerts From Database</h3>
            <div class="compact-list">
                <?php $__empty_1 = true; $__currentLoopData = $recentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e($notification->link ?: route('notifications')); ?>">
                        <strong><?php echo e($notification->title); ?></strong>
                        <span><?php echo e(Str::limit($notification->message, 75)); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted">No alerts yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\freelance_marketplace_laravel\resources\views/dashboard.blade.php ENDPATH**/ ?>