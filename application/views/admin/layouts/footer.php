<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<footer class="main-footer">
    <strong>&copy; <?php echo date('Y'); ?> Hospital Management System.</strong>
    <div class="float-right d-none d-sm-inline-block">
        Admin Dashboard Foundation
    </div>
</footer>

<aside class="control-sidebar control-sidebar-dark"></aside>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.3/js/jquery.overlayScrollbars.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
window.HMS_CSRF_NAME = '<?php echo $this->security->get_csrf_token_name(); ?>';
window.HMS_CSRF_HASH = '<?php echo $this->security->get_csrf_hash(); ?>';
</script>
<script src="<?php echo base_url('assets/js/hms-ajax.js'); ?>"></script>

</body>
</html>
