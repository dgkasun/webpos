<div class="page-header">
    <div>
        <h1><?= htmlspecialchars($pageTitle ?? 'WebPOS') ?></h1>
        <p>Welcome, <img src="assets/images/circle-user-solid-full.svg"> <?= htmlspecialchars($_SESSION['user_name']) ?>.</p>
    </div>
</div>