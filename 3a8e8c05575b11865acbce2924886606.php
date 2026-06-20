<?php $__env->startSection('title', 'Register'); ?>
<?php $__env->startSection('content'); ?>
<div class="form-container">
    <h2>Create Account</h2>
    <form method="post" action="<?php echo e(route('register.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="form-group"><label>Name</label><input name="name" value="<?php echo e(old('name')); ?>" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?php echo e(old('email')); ?>" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" name="password_confirmation" required></div>
        <div class="form-group"><label>I want to</label><select name="role"><option value="client">Hire Freelancers</option><option value="freelancer">Offer Services</option></select></div>
        <button class="btn btn-primary btn-block">Register</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\freelance_marketplace_laravel\resources\views/auth/register.blade.php ENDPATH**/ ?>