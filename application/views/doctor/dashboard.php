<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row g-4">
    <div class="col-12">
        <div class="card welcome-card">
            <div class="card-body p-4">
                <div class="section-label mb-2">Doctor Panel</div>
                <h1 class="h3 mb-2">Welcome, <?php echo html_escape($current_user_name); ?></h1>
                <p class="text-muted mb-0">
                    This is the protected doctor dashboard placeholder for Phase 1 authentication validation.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card placeholder-card">
            <div class="card-body p-4">
                <div class="section-label mb-2">Session</div>
                <h2 class="h5 mb-3">Authenticated Role Access</h2>
                <p class="text-muted mb-0">
                    Only users with the doctor role can reach this page through the shared login system.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card placeholder-card">
            <div class="card-body p-4">
                <div class="section-label mb-2">Future Module</div>
                <h2 class="h5 mb-3">Appointments and Prescriptions</h2>
                <p class="text-muted mb-0">
                    Clinical workflow screens will be introduced later according to the locked roadmap.
                </p>
            </div>
        </div>
    </div>
</div>
