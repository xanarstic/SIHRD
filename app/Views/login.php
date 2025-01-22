<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= $setting['namawebsite']; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="<?= base_url('public/' . $setting['iconlogin']) ?>" type="image/png">

    <style>
        body {
            background-color: #f8f9fa;
            background-image: url('<?= base_url('uploads/' . $setting['iconlogin']) ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-container h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            border-radius: 5px;
            padding: 15px;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }

        .alert {
            text-align: center;
            margin-top: 15px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            color: #6c757d;
        }

        .register-link {
            display: block;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-container">
            <h3>Login to <?= $setting['namawebsite']; ?></h3>

            <!-- Error Message (if any) -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
            <?php endif; ?>

            <form action="/home/loginProcess" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <?php if ($captchaMode === 'online'): ?>
                    <!-- Google reCAPTCHA -->
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="6LcZjL8qAAAAAHAb6nnyl58ZrcBnPoVMUbcdICTf"></div>
                    </div>
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <?php else: ?>
                    <!-- Manual CAPTCHA -->
                    <div class="mb-3">
                        <label for="captcha" class="form-label">Enter the text below:</label>
                        <div class="d-flex align-items-center">
                            <img src="<?= base_url('home/generateCaptcha'); ?>" alt="CAPTCHA Image" class="me-3">
                            <input type="text" class="form-control" id="captcha" name="captcha" required>
                        </div>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Login</button>
            </form>

            <div class="footer">
                <p>Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal"
                        class="text-primary">Register here</a></p>
                <p>&copy; <?= date('Y'); ?> <?= $setting['namawebsite']; ?>. All Rights Reserved.</p>
            </div>
        </div>
    </div>

    <!-- Modal Register -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm" action="/home/registerProcess" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="registerUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="registerUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="registerEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerPhone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="registerPhone" name="nohp" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="registerPassword" name="password" required>
                        </div>
                        <?php if ($captchaMode === 'online'): ?>
                            <!-- Google reCAPTCHA -->
                            <div class="mb-3">
                                <div class="g-recaptcha" data-sitekey="6LcZjL8qAAAAAHAb6nnyl58ZrcBnPoVMUbcdICTf"></div>
                            </div>
                            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                        <?php else: ?>
                            <!-- Manual CAPTCHA -->
                            <div class="mb-3">
                                <label for="captcha" class="form-label">Enter the text below:</label>
                                <div class="d-flex align-items-center">
                                    <img src="<?= base_url('home/generateCaptcha'); ?>" alt="CAPTCHA Image" class="me-3">
                                    <input type="text" class="form-control" id="captcha" name="captcha" required>
                                </div>
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>

</html>