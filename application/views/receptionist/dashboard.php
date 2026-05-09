<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row g-4">
    <div class="col-12">
        <div class="card welcome-card">
            <div class="card-body p-4">
                <div class="section-label mb-2">Receptionist Panel</div>
                <h1 class="h3 mb-2">Welcome, <?php echo html_escape($current_user_name); ?></h1>
                <p class="text-muted mb-0">
                    This dashboard confirms receptionist authentication and role protection are functioning correctly.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card placeholder-card">
            <div class="card-body p-4">
                <div class="section-label mb-2">Access Control</div>
                <h2 class="h5 mb-3">Role Gate Active</h2>
                <p class="text-muted mb-0">
                    Shared login is complete, and unauthorized users are blocked from receptionist-only routes.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card placeholder-card">
            <div class="card-body p-4">
                <div class="section-label mb-2">Future Module</div>
                <h2 class="h5 mb-3">Patient and Appointment Workflow</h2>
                <p class="text-muted mb-0">
                    Patient registration and booking screens will be added in their scheduled phases only.
                </p>
            </div>
        </div>
    </div>
</div>
