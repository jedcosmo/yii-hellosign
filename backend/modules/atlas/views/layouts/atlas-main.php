<?= $this->render('header') ?>
<?= $this->render('sidebar', array('settings' => $this->params['sidebar_settings'])) ?>
<!-- Page Content -->
<div id="page-wrapper">
        <?= $this->render('form') ?>
</div>

<?= $this->render('footer') ?>
