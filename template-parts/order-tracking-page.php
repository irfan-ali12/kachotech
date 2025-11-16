<?php
/**
 * Modern Order Tracking Page Template
 * Displays a form to track orders and shows order details/status
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) {
    echo '<div class="kt-tracking-error"><p>WooCommerce is not active. Please contact support.</p></div>';
    return;
}

$order_id = '';
$order_email = '';
$order = null;
$error_message = '';

// Handle form submission
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['kt_track_order_nonce'] ) ) {
    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['kt_track_order_nonce'], 'kt_track_order' ) ) {
        $error_message = 'Security verification failed. Please try again.';
    } else {
        $order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
        $order_email = isset( $_POST['order_email'] ) ? sanitize_email( $_POST['order_email'] ) : '';

        if ( ! $order_id || ! $order_email ) {
            $error_message = 'Please enter both Order ID and Email address.';
        } else {
            // Try to get the order (support formats like "#12345" or plain numbers)
            $order_id_clean = preg_replace( '/[^0-9]/', '', $order_id );
            if ( $order_id_clean !== '' ) {
                $order = wc_get_order( intval( $order_id_clean ) );
            } else {
                $order = wc_get_order( $order_id );
            }

            if ( ! $order ) {
                $error_message = 'No order found with that Order ID.';
            } elseif ( $order->get_billing_email() !== $order_email ) {
                $error_message = 'The email address does not match the order. Please check and try again.';
            }
        }
    }
}

// Also check if order ID is in URL (GET parameter)
if ( ! $order && isset( $_GET['order_id'] ) && isset( $_GET['order_email'] ) ) {
    $order_id = sanitize_text_field( $_GET['order_id'] );
    $order_email = sanitize_email( $_GET['order_email'] );
    $order_id_clean = preg_replace( '/[^0-9]/', '', $order_id );
    if ( $order_id_clean !== '' ) {
        $order = wc_get_order( intval( $order_id_clean ) );
    } else {
        $order = wc_get_order( $order_id );
    }

    if ( $order && $order->get_billing_email() === $order_email ) {
        // Valid order, display it
    } else {
        $error_message = 'No matching order found.';
        $order = null;
    }
}
?>

<div class="kt-order-tracking-container">
    <style>
        .kt-order-tracking-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .kt-tracking-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .kt-tracking-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #151821;
            margin-bottom: 10px;
        }

        .kt-tracking-header p {
            color: #6b7280;
            font-size: 14px;
        }

        .kt-tracking-form-wrapper {
            background: #f9fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .kt-tracking-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 16px;
            align-items: flex-end;
        }

        .kt-form-group {
            display: flex;
            flex-direction: column;
        }

        .kt-form-group label {
            font-weight: 600;
            font-size: 14px;
            color: #252732;
            margin-bottom: 8px;
        }

        .kt-form-group input {
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.25s ease;
        }

        .kt-form-group input:focus {
            outline: none;
            border-color: #ff2446;
            box-shadow: 0 0 0 3px rgba(255, 36, 70, 0.1);
        }

        .kt-tracking-btn {
            padding: 12px 28px;
            background: #ff2446;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.25s ease;
        }

        .kt-tracking-btn:hover {
            background: #e00036;
        }

        .kt-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .kt-success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .kt-order-details {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .kt-order-header {
            background: linear-gradient(135deg, #ff2446 0%, #e00036 100%);
            color: white;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .kt-order-number {
            font-size: 20px;
            font-weight: 700;
        }

        .kt-order-status-badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kt-order-status-badge.completed {
            background: #dcfce7;
            color: #166534;
        }

        .kt-order-status-badge.processing {
            background: #fef3c7;
            color: #b45309;
        }

        .kt-order-status-badge.pending {
            background: #e0e7ff;
            color: #3730a3;
        }

        .kt-order-timeline {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .kt-timeline-item {
            display: flex;
            gap: 20px;
            margin-bottom: 24px;
            position: relative;
        }

        .kt-timeline-item:last-child {
            margin-bottom: 0;
        }

        .kt-timeline-item::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 32px;
            width: 2px;
            height: calc(100% + 24px);
            background: #e5e7eb;
        }

        .kt-timeline-item:last-child::before {
            display: none;
        }

        .kt-timeline-marker {
            width: 32px;
            height: 32px;
            background: #ff2446;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .kt-timeline-marker.completed {
            background: #22c55e;
        }

        .kt-timeline-marker.active {
            background: #ffb020; /* amber for active */
            color: #111827;
        }

        .kt-timeline-marker.upcoming {
            background: #f3f4f6; /* light gray */
            color: #9ca3af;
            font-weight: 600;
        }

        .kt-timeline-content h4 {
            font-weight: 700;
            color: #151821;
            margin: 0 0 4px 0;
        }

        .kt-timeline-content p {
            color: #6b7280;
            font-size: 13px;
            margin: 0;
        }

        .kt-order-details-grid {
            padding: 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }

        .kt-detail-section h3 {
            font-weight: 700;
            font-size: 14px;
            color: #151821;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kt-detail-item {
            margin-bottom: 12px;
        }

        .kt-detail-label {
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .kt-detail-value {
            color: #151821;
            font-size: 14px;
            font-weight: 500;
            margin-top: 4px;
        }

        .kt-order-items {
            padding: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .kt-items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kt-items-table thead {
            background: #f9fafc;
            border-bottom: 2px solid #e5e7eb;
        }

        .kt-items-table th {
            padding: 12px;
            text-align: left;
            font-weight: 700;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kt-items-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #151821;
        }

        .kt-items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .kt-item-name {
            font-weight: 600;
        }

        .kt-item-qty {
            text-align: center;
            color: #6b7280;
        }

        .kt-item-price {
            text-align: right;
            font-weight: 600;
        }

        .kt-order-totals {
            padding: 24px;
            background: #f9fafc;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
        }

        .kt-totals-summary {
            width: 100%;
            max-width: 300px;
        }

        .kt-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .kt-total-row.grand-total {
            font-size: 16px;
            font-weight: 700;
            color: #ff2446;
            border-top: 2px solid #e5e7eb;
            padding-top: 12px;
        }

        @media (max-width: 768px) {
            .kt-tracking-form {
                grid-template-columns: 1fr;
            }

            .kt-order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .kt-order-details-grid {
                grid-template-columns: 1fr;
            }

            .kt-items-table {
                font-size: 12px;
            }

            .kt-items-table th,
            .kt-items-table td {
                padding: 8px;
            }
        }
    </style>

    <div class="kt-tracking-header">
        <h1>Track Your Order</h1>
        <p>Enter your Order ID and Email to check the status of your order</p>
    </div>

    <!-- Search Form -->
    <div class="kt-tracking-form-wrapper">
        <form method="POST" class="kt-tracking-form">
            <?php wp_nonce_field( 'kt_track_order', 'kt_track_order_nonce' ); ?>

            <div class="kt-form-group">
                <label for="order_id">Order ID</label>
                <input
                    type="text"
                    id="order_id"
                    name="order_id"
                    placeholder="e.g., #12345"
                    value="<?php echo esc_attr( $order_id ); ?>"
                    required
                />
            </div>

            <div class="kt-form-group">
                <label for="order_email">Email Address</label>
                <input
                    type="email"
                    id="order_email"
                    name="order_email"
                    placeholder="your@email.com"
                    value="<?php echo esc_attr( $order_email ); ?>"
                    required
                />
            </div>

            <button type="submit" class="kt-tracking-btn">Track Order</button>
        </form>
    </div>

    <!-- Error Message -->
    <?php if ( $error_message ) : ?>
        <div class="kt-error">
            <strong>Error:</strong> <?php echo esc_html( $error_message ); ?>
        </div>
    <?php endif; ?>

    <!-- Order Details -->
    <?php if ( $order ) : ?>
        <div class="kt-success">
            ✓ Order found! Here are your order details and current status.
        </div>

        <div class="kt-order-details">
            <!-- Order Header -->
            <div class="kt-order-header">
                <div class="kt-order-number">
                    Order #<?php echo esc_html( $order->get_id() ); ?>
                </div>
                <span class="kt-order-status-badge <?php echo esc_attr( $order->get_status() ); ?>">
                    <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                </span>
                <div style="margin-left: auto; text-align: right; font-size: 12px; opacity: 0.8;">
                    <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="kt-order-timeline">
                <h3 style="margin-bottom: 20px; font-weight: 700; color: #151821;">Order Timeline</h3>

                <?php
                // Timeline steps in logical order
                $timeline_steps = array(
                    array( 'key' => 'pending',    'label' => 'Order Received' ),
                    array( 'key' => 'processing', 'label' => 'Processing' ),
                    array( 'key' => 'shipped',    'label' => 'Out for Delivery' ),
                    array( 'key' => 'completed',  'label' => 'Completed' ),
                );

                // Current order status
                $current_status = $order->get_status();

                // Map some statuses to timeline keys if needed
                if ( $current_status === 'on-hold' ) {
                    $current_status = 'processing';
                }

                // Determine index of current status in timeline (fallback to -1)
                $step_keys = array_column( $timeline_steps, 'key' );
                $current_index = array_search( $current_status, $step_keys, true );
                if ( $current_index === false ) {
                    // Unknown status (cancelled/refunded) -> no progress
                    $current_index = -1;
                }

                // Render timeline items
                foreach ( $timeline_steps as $i => $step ) {
                    if ( $i < $current_index ) {
                        $state = 'completed';
                    } elseif ( $i === $current_index ) {
                        $state = 'active';
                    } else {
                        $state = 'upcoming';
                    }
                    ?>
                    <div class="kt-timeline-item">
                        <div class="kt-timeline-marker <?php echo esc_attr( $state ); ?>">
                            <?php if ( $state === 'completed' ) : ?>
                                ✓
                            <?php elseif ( $state === 'active' ) : ?>
                                ●
                            <?php else : ?>
                                ○
                            <?php endif; ?>
                        </div>
                        <div class="kt-timeline-content">
                            <h4><?php echo esc_html( $step['label'] ); ?></h4>
                            <p>
                                <?php
                                if ( $state === 'completed' ) {
                                    echo 'Completed';
                                } elseif ( $state === 'active' ) {
                                    echo 'Current Status - ' . esc_html( wc_get_order_status_name( $current_status ) );
                                } else {
                                    echo 'Pending';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- Order Info Grid -->
            <div class="kt-order-details-grid">
                <!-- Billing Address -->
                <div class="kt-detail-section">
                    <h3>Billing Address</h3>
                    <div class="kt-detail-item">
                        <div class="kt-detail-label">Name</div>
                        <div class="kt-detail-value">
                            <?php echo esc_html( $order->get_formatted_billing_full_name() ); ?>
                        </div>
                    </div>
                    <div class="kt-detail-item">
                        <div class="kt-detail-label">Email</div>
                        <div class="kt-detail-value">
                            <?php echo esc_html( $order->get_billing_email() ); ?>
                        </div>
                    </div>
                    <div class="kt-detail-item">
                        <div class="kt-detail-label">Phone</div>
                        <div class="kt-detail-value">
                            <?php echo esc_html( $order->get_billing_phone() ); ?>
                        </div>
                    </div>
                    <div class="kt-detail-item">
                        <div class="kt-detail-label">Address</div>
                        <div class="kt-detail-value">
                            <?php echo wp_kses_post( $order->get_formatted_billing_address() ); ?>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="kt-detail-section">
                    <h3>Shipping Address</h3>
                    <div class="kt-detail-item">
                        <div class="kt-detail-label">Name</div>
                        <div class="kt-detail-value">
                            <?php echo esc_html( $order->get_formatted_shipping_full_name() ); ?>
                        </div>
                    </div>
                    <div class="kt-detail-item">
                        <div class="kt-detail-label">Phone</div>
                        <div class="kt-detail-value">
                            <?php echo esc_html( $order->get_shipping_phone() ?: 'N/A' ); ?>
                        </div>
                    </div>
                    <div class="kt-detail-item">
                        <div class="kt-detail-label">Address</div>
                        <div class="kt-detail-value">
                            <?php echo wp_kses_post( $order->get_formatted_shipping_address() ); ?>
                        </div>
                    </div>
                    <div class="kt-detail-item">
                        <div class="kt-detail-label">Shipping Method</div>
                        <div class="kt-detail-value">
                            <?php echo esc_html( $order->get_shipping_method() ); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="kt-order-items">
                <h3 style="margin-bottom: 16px; font-weight: 700; color: #151821;">Items Ordered</h3>
                <table class="kt-items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="kt-item-qty">Quantity</th>
                            <th class="kt-item-price">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ( $order->get_items() as $item ) {
                            $product = $item->get_product();
                            ?>
                            <tr>
                                <td class="kt-item-name">
                                    <?php echo esc_html( $item->get_name() ); ?>
                                </td>
                                <td class="kt-item-qty">
                                    <?php echo esc_html( $item->get_quantity() ); ?>
                                </td>
                                <td class="kt-item-price">
                                    <?php echo wp_kses_post( wc_price( $item->get_total() ) ); ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Order Totals -->
            <div class="kt-order-totals">
                <div class="kt-totals-summary">
                    <div class="kt-total-row">
                        <span>Subtotal:</span>
                        <span><?php echo wp_kses_post( wc_price( $order->get_subtotal() ) ); ?></span>
                    </div>
                    <?php if ( $order->get_total_shipping() > 0 ) : ?>
                        <div class="kt-total-row">
                            <span>Shipping:</span>
                            <span><?php echo wp_kses_post( wc_price( $order->get_total_shipping() ) ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ( $order->get_total_tax() > 0 ) : ?>
                        <div class="kt-total-row">
                            <span>Tax:</span>
                            <span><?php echo wp_kses_post( wc_price( $order->get_total_tax() ) ); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="kt-total-row grand-total">
                        <span>Total:</span>
                        <span><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
