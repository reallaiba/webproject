<?php $__env->startSection('title', 'Cart'); ?>
<?php $__env->startSection('content'); ?>
<?php ($total = $items->sum(fn($item) => $item->quantity * $item->gig->price)); ?>

<div class="page-head">
    <div>
        <h2>Cart</h2>
        <p class="text-muted">Gigs added from the marketplace will appear here for checkout.</p>
    </div>
    <a class="btn btn-primary" href="<?php echo e(route('gigs.index')); ?>">Browse Gigs</a>
</div>

<?php if($items->count()): ?>
    <div class="table-container cart-table">
        <table>
            <thead>
                <tr>
                    <th>Gig</th>
                    <th>Freelancer</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <strong><?php echo e($item->gig->title); ?></strong>
                            <div class="text-muted"><?php echo e($item->gig->delivery_days); ?> days delivery</div>
                        </td>
                        <td><?php echo e($item->gig->freelancer->name); ?></td>
                        <td><?php echo e($item->quantity); ?></td>
                        <td>$<?php echo e(number_format($item->quantity * $item->gig->price, 2)); ?></td>
                        <td>
                            <form method="post" action="<?php echo e(route('cart.remove', $item)); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('delete'); ?>
                                <button class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="cart-total-label">Total</td>
                    <td colspan="2" class="cart-total">$<?php echo e(number_format($total, 2)); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="cart-actions">
        <a class="btn btn-success" href="<?php echo e(route('checkout')); ?>">Checkout</a>
    </div>
<?php else: ?>
    <div class="cart-empty-state card">
        <h3>Your cart is empty</h3>
        <p>Add any gig from the list below. After adding, this page will show gig name, quantity, price, total, and checkout button.</p>
    </div>
<?php endif; ?>

<?php if($suggestedGigs->count()): ?>
    <section class="section">
        <div class="page-head">
            <h2><?php echo e($items->count() ? 'More Gigs' : 'Available Gigs'); ?></h2>
        </div>
        <div class="cards">
            <?php $__currentLoopData = $suggestedGigs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card">
                    <h3><?php echo e($gig->title); ?></h3>
                    <p><?php echo e(Str::limit($gig->description, 120)); ?></p>
                    <p class="text-muted">By <?php echo e($gig->freelancer->name); ?> - <?php echo e($gig->delivery_days); ?> days</p>
                    <p class="card-price">$<?php echo e(number_format($gig->price, 2)); ?></p>
                    <form method="post" action="<?php echo e(route('cart.add', $gig)); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\freelance_marketplace_laravel\resources\views/cart/index.blade.php ENDPATH**/ ?>