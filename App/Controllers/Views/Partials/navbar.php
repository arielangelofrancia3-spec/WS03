<?php 
use Framework\Session;
?>

<header class="bg-blue-900 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        
        <h1 class="text-3xl font-semibold">
            <a href="<?= url('') ?>">Jobseek</a>
        </h1>
        <nav class="space-x-4">
            <?php if(Session::has('user')): ?>
               <div class="flex items-center gap-4">
                <div>Welcome <?= Session::get('user')['name'] ?></div>
                <form method="POST" action="<?= url('auth/logout') ?>">
                    <button type="submit" formaction="<?= url('auth/logout') ?>" class="text-white hover:underline">Logout</button>
                </form>
               
                <a href="<?= url('listings/create') ?>" class="btn btn-primary nav-cta">
                    <i class="fa fa-edit"></i>
                    <span>Post a Job</span>
                </a>
                </div>
            <?php else: ?>
                <a href="<?= url('auth/login') ?>" class="text-white hover:underline">Login</a>
                <a href="<?= url('auth/register') ?>" class="text-white hover:underline">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>