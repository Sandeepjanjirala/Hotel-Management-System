<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo html_escape($page_title); ?> | HMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e8f1ff 0%, #f8fbff 55%, #eef4f8 100%);
        }

        .login-wrapper {
            min-height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 1rem 3rem rgba(15, 23, 42, 0.12);
        }

        .brand-title {
            font-size: 1.7rem;
            font-weight: 700;
            color: #0d6efd;
        }

        .brand-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
        }

        .form-label {
            font-weight: 600;
        }

        .btn-login {
            min-height: 46px;
            font-weight: 600;
        }

        .validation-block p {
            margin-bottom: 0.35rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center login-wrapper">
        <div class="col-12 d-flex justify-content-center">
            <div class="card login-card">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="brand-title">HMS Login</div>
                        <div class="brand-subtitle">Sign in to access your hospital panel</div>
                    </div>

                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo html_escape($this->session->flashdata('success')); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo html_escape($this->session->flashdata('error')); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (validation_errors()): ?>
                        <div class="alert alert-danger validation-block" role="alert">
                            <?php echo validation_errors(); ?>
                        </div>
                    <?php endif; ?>

                    <?php echo form_open('auth/login', array('id' => 'loginForm', 'autocomplete' => 'off', 'novalidate' => 'novalidate')); ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input
                                type="email"
                                class="form-control form-control-lg"
                                id="email"
                                name="email"
                                value="<?php echo set_value('email'); ?>"
                                placeholder="Enter your email"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                class="form-control form-control-lg"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                required
                            >
                        </div>

                        <div class="d-grid">
                            <button type="submit" id="loginButton" class="btn btn-primary btn-lg btn-login">
                                Login
                            </button>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"
></script>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>
<script>
    $(function () {
        $('#loginForm').on('submit', function () {
            $('#loginButton')
                .prop('disabled', true)
                .text('Signing in...');
        });
    });
</script>
</body>
</html>
