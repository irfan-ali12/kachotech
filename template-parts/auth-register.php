<?php
/**
 * Professional Registration Page
 * Custom template for user registration with modern UI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Redirect if already logged in
if ( is_user_logged_in() ) {
    wp_redirect( wc_get_page_permalink( 'myaccount' ) );
    exit;
}

// Check if registration is enabled
if ( ! get_option( 'users_can_register' ) ) {
    echo '<div style="padding:40px; text-align:center;"><p>User registration is currently disabled.</p></div>';
    return;
}

$register_error = '';
$register_success = false;
$reg_email = '';
$reg_username = '';

// Handle registration form submission
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['kt_register_nonce'] ) ) {
    if ( ! wp_verify_nonce( $_POST['kt_register_nonce'], 'kt_register' ) ) {
        $register_error = 'Security verification failed. Please try again.';
    } else {
        $reg_username = isset( $_POST['reg_username'] ) ? sanitize_user( $_POST['reg_username'] ) : '';
        $reg_email = isset( $_POST['reg_email'] ) ? sanitize_email( $_POST['reg_email'] ) : '';
        $reg_password = isset( $_POST['reg_password'] ) ? $_POST['reg_password'] : '';
        $reg_confirm = isset( $_POST['reg_confirm'] ) ? $_POST['reg_confirm'] : '';

        // Validation
        if ( empty( $reg_username ) || empty( $reg_email ) || empty( $reg_password ) ) {
            $register_error = 'Please fill in all fields.';
        } elseif ( strlen( $reg_password ) < 6 ) {
            $register_error = 'Password must be at least 6 characters long.';
        } elseif ( $reg_password !== $reg_confirm ) {
            $register_error = 'Passwords do not match.';
        } elseif ( username_exists( $reg_username ) ) {
            $register_error = 'Username already exists. Please choose a different one.';
        } elseif ( email_exists( $reg_email ) ) {
            $register_error = 'Email already registered. Try logging in instead.';
        } else {
            // Create user
            $user_id = wp_create_user( $reg_username, $reg_password, $reg_email );

            if ( is_wp_error( $user_id ) ) {
                $register_error = 'Registration failed: ' . $user_id->get_error_message();
            } else {
                // Set user role to customer
                $user = new WP_User( $user_id );
                $user->set_role( 'customer' );

                // Optionally log them in
                wp_set_current_user( $user_id );
                wp_set_auth_cookie( $user_id );
                do_action( 'wp_login', $reg_username, $user );

                // Redirect to account page
                wp_redirect( wc_get_page_permalink( 'myaccount' ) );
                exit;
            }
        }
    }
}
?>

<div class="kt-auth-page kt-register-page">
    <style>
        .kt-auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f9fafc 0%, #ffffff 50%, #f9fafc 100%);
            padding: 20px;
        }

        .kt-auth-container {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .kt-auth-header {
            background: linear-gradient(135deg, #ff2446 0%, #e00036 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .kt-auth-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .kt-auth-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .kt-auth-body {
            padding: 40px 30px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .kt-form-group {
            margin-bottom: 20px;
        }

        .kt-form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #151821;
            margin-bottom: 8px;
        }

        .kt-form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.25s ease;
            box-sizing: border-box;
        }

        .kt-form-group input:focus {
            outline: none;
            border-color: #ff2446;
            box-shadow: 0 0 0 3px rgba(255, 36, 70, 0.1);
        }

        .kt-password-meter {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin-top: 6px;
            overflow: hidden;
        }

        .kt-password-meter-bar {
            height: 100%;
            background: #ef4444;
            transition: width 0.25s ease, background 0.25s ease;
            width: 0;
        }

        .kt-password-meter-bar.weak {
            width: 33%;
            background: #f97316;
        }

        .kt-password-meter-bar.fair {
            width: 66%;
            background: #eab308;
        }

        .kt-password-meter-bar.strong {
            width: 100%;
            background: #22c55e;
        }

        .kt-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .kt-register-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ff2446 0%, #e00036 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 6px 18px rgba(255, 36, 70, 0.12);
        }

        .kt-register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(255, 36, 70, 0.16);
        }

        .kt-register-btn:active {
            transform: translateY(0);
        }

        .kt-form-footer {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .kt-form-footer p {
            font-size: 13px;
            color: #6b7280;
            margin: 0 0 12px 0;
        }

        .kt-form-footer a {
            color: #ff2446;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.25s ease;
        }

        .kt-form-footer a:hover {
            color: #e00036;
        }

        @media (max-width: 480px) {
            .kt-auth-container {
                box-shadow: none;
                border-radius: 0;
            }

            .kt-auth-header {
                padding: 30px 20px;
            }

            .kt-auth-header h1 {
                font-size: 24px;
            }

            .kt-auth-body {
                padding: 30px 20px;
            }
        }
    </style>

    <div class="kt-auth-container">
        <!-- Header -->
        <div class="kt-auth-header">
            <h1>Join KachoTech</h1>
            <p>Create your account to start shopping</p>
        </div>

        <!-- Body -->
        <div class="kt-auth-body">
            <!-- Error Message -->
            <?php if ( $register_error ) : ?>
                <div class="kt-error">
                    <?php echo esc_html( $register_error ); ?>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" class="kt-register-form">
                <?php wp_nonce_field( 'kt_register', 'kt_register_nonce' ); ?>

                <div class="kt-form-group">
                    <label for="reg_username">Username</label>
                    <input
                        type="text"
                        id="reg_username"
                        name="reg_username"
                        value="<?php echo esc_attr( $reg_username ); ?>"
                        placeholder="Choose a username"
                        required
                    />
                </div>

                <div class="kt-form-group">
                    <label for="reg_email">Email Address</label>
                    <input
                        type="email"
                        id="reg_email"
                        name="reg_email"
                        value="<?php echo esc_attr( $reg_email ); ?>"
                        placeholder="your@email.com"
                        required
                    />
                </div>

                <div class="kt-form-group">
                    <label for="reg_password">Password</label>
                    <input
                        type="password"
                        id="reg_password"
                        name="reg_password"
                        placeholder="••••••••"
                        minlength="6"
                        required
                    />
                    <div class="kt-password-meter">
                        <div class="kt-password-meter-bar" id="passwordMeter"></div>
                    </div>
                </div>

                <div class="kt-form-group">
                    <label for="reg_confirm">Confirm Password</label>
                    <input
                        type="password"
                        id="reg_confirm"
                        name="reg_confirm"
                        placeholder="••••••••"
                        minlength="6"
                        required
                    />
                </div>

                <button type="submit" class="kt-register-btn">Create Account</button>

                <div class="kt-form-footer">
                    <p>Already have an account?</p>
                    <a href="<?php echo esc_url( home_url( '/login/' ) ); ?>">Sign In</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password strength meter
        const passwordInput = document.getElementById('reg_password');
        const passwordMeter = document.getElementById('passwordMeter');

        if (passwordInput && passwordMeter) {
            passwordInput.addEventListener('input', function() {
                const strength = calculatePasswordStrength(this.value);
                passwordMeter.className = 'kt-password-meter-bar ' + strength;
            });
        }

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            if (strength <= 2) return 'weak';
            if (strength <= 3) return 'fair';
            return 'strong';
        }
    </script>
</div>
