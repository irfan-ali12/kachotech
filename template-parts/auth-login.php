<?php
/**
 * Professional Login Page
 * Custom template for user login with modern UI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Redirect if already logged in
if ( is_user_logged_in() ) {
    wp_redirect( wc_get_page_permalink( 'myaccount' ) );
    exit;
}

$login_error = '';
$login_user = '';

// Handle login form submission
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['kt_login_nonce'] ) ) {
    if ( ! wp_verify_nonce( $_POST['kt_login_nonce'], 'kt_login' ) ) {
        $login_error = 'Security verification failed. Please try again.';
    } else {
        $login_user = isset( $_POST['login_user'] ) ? sanitize_user( $_POST['login_user'] ) : '';
        $login_password = isset( $_POST['login_password'] ) ? $_POST['login_password'] : '';

        if ( empty( $login_user ) || empty( $login_password ) ) {
            $login_error = 'Please enter both username/email and password.';
        } else {
            $user = wp_authenticate( $login_user, $login_password );

            if ( is_wp_error( $user ) ) {
                $login_error = 'Invalid username or password. Please try again.';
            } else {
                wp_set_current_user( $user->ID );
                wp_set_auth_cookie( $user->ID );
                do_action( 'wp_login', $user->user_login, $user );

                wp_redirect( wc_get_page_permalink( 'myaccount' ) );
                exit;
            }
        }
    }
}
?>

<div class="kt-auth-page kt-login-page">
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

        .kt-login-btn {
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

        .kt-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(255, 36, 70, 0.16);
        }

        .kt-login-btn:active {
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

        .kt-forgot-link {
            display: inline-block;
            font-size: 12px;
            color: #ff2446;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.25s ease;
            margin-top: 12px;
        }

        .kt-forgot-link:hover {
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
            <h1>Welcome Back</h1>
            <p>Sign in to your KachoTech account</p>
        </div>

        <!-- Body -->
        <div class="kt-auth-body">
            <!-- Error Message -->
            <?php if ( $login_error ) : ?>
                <div class="kt-error">
                    <?php echo esc_html( $login_error ); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" class="kt-login-form">
                <?php wp_nonce_field( 'kt_login', 'kt_login_nonce' ); ?>

                <div class="kt-form-group">
                    <label for="login_user">Email or Username</label>
                    <input
                        type="text"
                        id="login_user"
                        name="login_user"
                        value="<?php echo esc_attr( $login_user ); ?>"
                        placeholder="your@email.com"
                        required
                    />
                </div>

                <div class="kt-form-group">
                    <label for="login_password">Password</label>
                    <input
                        type="password"
                        id="login_password"
                        name="login_password"
                        placeholder="••••••••"
                        required
                    />
                </div>

                <button type="submit" class="kt-login-btn">Sign In</button>

                <div class="kt-form-footer">
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="kt-forgot-link">Forgot Password?</a>

                    <p>Don't have an account?</p>
                    <a href="<?php echo esc_url( home_url( '/register/' ) ); ?>">Create Account</a>
                </div>
            </form>
        </div>
    </div>
</div>
