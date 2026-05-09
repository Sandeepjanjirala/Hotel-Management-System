<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row g-4">
    <div class="col-12">
        <div class="card welcome-card">
            <div class="card-body p-4">
                <div class="section-label mb-2">Patient Panel</div>
                <h1 class="h3 mb-2">Welcome, <?php echo html_escape($current_user_name); ?></h1>
                <p class="text-muted mb-0">
                    This is the Phase 1 patient dashboard placeholder connected to the shared secure login flow.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card placeholder-card">
            <div class="card-body p-4">
                <div class="section-label mb-2">Authentication</div>
                <h2 class="h5 mb-3">Protected Session Active</h2>
                <p class="text-muted mb-0">
                    Your login session is active and patient-only route access is being enforced by the base controller.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card placeholder-card">
            <div class="card-body p-4">
                <div class="section-label mb-2">Future Module</div>
                <h2 class="h5 mb-3">Records and Billing View</h2>
                <p class="text-muted mb-0">
                    Patient-facing medical history and billing visibility will be introduced in later phases.
                </p>
            </div>
        </div>
    </div>
</div>
