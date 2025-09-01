<?php partial('partials/header', ['title' => 'My Profile']) ?>

<div class="max-w-xl mx-auto space-y-6 p-4">

    <h1 class="text-2xl font-bold mb-4">My Profile</h1>

    <!-- Profile Info Section -->
    <div id="profile-info">
        <?php partial('profile/partials/profile_info', ['user' => $user]); ?>
    </div>

    <!-- Change Password Section -->
    <div id="profile-password">
        <?php partial('profile/partials/profile_password'); ?>
    </div>

</div>

<?php partial('partials/footer') ?>
