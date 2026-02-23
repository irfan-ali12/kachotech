<?php
/**
 * Template Name: Contact Us
 * Description: Contact Us page template with form, contact info, and map
 */

get_header();
?>

<!-- Optional (if Inter not already loaded by theme) -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
  :root {
    --kt-primary: #ff2446;
    --kt-primary-soft: rgba(255, 36, 70, 0.08);
    --kt-dark: #111827;
    --kt-text: #111827;
    --kt-muted: #6b7280;
    --kt-border: #e5e7eb;
    --kt-bg: #f3fafb;
  }

  .kt-contact-page {
    font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--kt-text);
    background: var(--kt-bg);
    padding: 80px 0px 0px 0px;
  }

  .kt-contact-inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 16px;
  }

  /* BREADCRUMB + PAGE TITLE */
  .kt-contact-breadcrumb {
    font-size: 13px;
    color: var(--kt-muted);
    margin-bottom: 4px;
  }

  .kt-contact-breadcrumb a {
    color: inherit;
    text-decoration: none;
  }

  .kt-contact-breadcrumb a:hover {
    color: var(--kt-primary);
  }

  .kt-contact-heading {
    font-size: 28px;
    font-weight: 600;
    margin: 0 0 4px;
  }

  .kt-contact-subtitle {
    font-size: 15px;
    color: var(--kt-muted);
    margin: 0 0 26px;
  }

  /* SECTION 2: MAIN WHITE PANEL */
  .kt-contact-main {
    background: #ffffff;
    border-radius: 24px;
    padding: 28px 28px 26px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.04);
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(0, 1.1fr) minmax(0, 1.1fr);
    gap: 32px;
    align-items: flex-start;
  }

  @media (max-width: 1024px) {
    .kt-contact-main {
      grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.2fr);
      grid-template-rows: auto auto;
    }
    .kt-contact-photo {
      grid-column: 1 / -1;
      max-width: 340px;
      justify-self: flex-start;
    }
  }

  @media (max-width: 768px) {
    .kt-contact-main {
      grid-template-columns: minmax(0, 1fr);
      grid-template-rows: auto auto auto;
      gap: 22px;
      padding: 22px 18px 22px;
    }
    .kt-contact-photo {
      max-width: 100%;
      justify-self: stretch;
    }
  }

  /* LEFT: FORM */
  .kt-form-title {
    font-size: 20px;
    font-weight: 600;
    margin: 0 0 4px;
  }

  .kt-form-text {
    font-size: 14px;
    color: var(--kt-muted);
    margin: 0 0 16px;
  }

  .kt-form-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 12px;
  }

  @media (max-width: 640px) {
    .kt-form-row {
      grid-template-columns: minmax(0, 1fr);
    }
  }

  .kt-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 14px;
  }

  .kt-field label {
    font-weight: 500;
    color: var(--kt-dark);
  }

  .kt-input,
  .kt-textarea {
    border-radius: 10px;
    border: 1px solid var(--kt-border);
    padding: 10px 12px;
    font-size: 14px;
    font-family: inherit;
    outline: none;
    background-color: #ffffff;
    transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
  }

  .kt-input::placeholder,
  .kt-textarea::placeholder {
    color: #9ca3af;
  }

  .kt-input:focus,
  .kt-textarea:focus {
    border-color: var(--kt-primary);
    box-shadow: 0 0 0 1px var(--kt-primary-soft);
    background-color: #ffffff;
  }

  .kt-textarea {
    resize: vertical;
    min-height: 140px;
  }

  .kt-form-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-top: 12px;
    font-size: 13px;
  }

  @media (max-width: 640px) {
    .kt-form-footer {
      flex-direction: column;
      align-items: flex-start;
    }
  }

  .kt-checkbox {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: var(--kt-muted);
  }

  .kt-checkbox input {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 1px solid var(--kt-border);
    accent-color: var(--kt-primary);
    cursor: pointer;
  }

  .kt-checkbox span a {
    color: var(--kt-primary);
    text-decoration: none;
  }

  .kt-checkbox span a:hover {
    text-decoration: underline;
  }

  .kt-button {
    border: none;
    border-radius: 999px;
    padding: 10px 26px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    background: var(--kt-primary);
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 12px 30px rgba(239, 68, 68, 0.35);
    transition: transform .13s ease, box-shadow .13s ease, background-color .13s ease;
    white-space: nowrap;
  }

  .kt-button:hover {
    background-color: #e1203f;
    transform: translateY(-1px);
    box-shadow: 0 16px 38px rgba(239, 68, 68, 0.45);
  }

  .kt-button:disabled {
    opacity: 0.65;
    cursor: default;
    transform: none;
    box-shadow: none;
  }

  .kt-button .kt-arrow {
    font-size: 16px;
    transform: translateY(1px);
  }

  .kt-form-message {
    margin-top: 10px;
    font-size: 13px;
    color: var(--kt-muted);
    min-height: 18px;
  }

  .kt-form-message--success {
    color: #16a34a;
  }

  .kt-form-message--error {
    color: #dc2626;
  }

  .kt-input-error {
    border-color: #f87171 !important;
    background-color: #fef2f2 !important;
  }

  /* MIDDLE: CONTACT INFO */
  .kt-contact-info-block h3 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 4px;
  }

  .kt-contact-info-block p {
    margin: 0 0 10px;
    font-size: 14px;
    color: var(--kt-muted);
  }

  .kt-contact-info-block + .kt-contact-info-block {
    margin-top: 14px;
  }

  .kt-contact-info-block a {
    color: var(--kt-primary);
    text-decoration: none;
  }

  .kt-contact-info-block a:hover {
    text-decoration: underline;
  }

  .kt-info-social {
    display: flex;
    gap: 8px;
    margin-top: 6px;
  }

  .kt-info-social a {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    border: 1px solid var(--kt-border);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: var(--kt-muted);
    background: #ffffff;
  }

  .kt-info-social a:hover {
    border-color: var(--kt-primary);
    color: var(--kt-primary);
    background: var(--kt-primary-soft);
  }

  /* RIGHT: PHOTO */
  .kt-contact-photo {
    border-radius: 20px;
    overflow: hidden;
    background: #000;
  }

  .kt-contact-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  /* SECTION 3: MAP */
  .kt-contact-map {
    margin-top: 34px;
    border-radius: 0; /* like reference: full-width band */
    overflow: hidden;
  }

  .kt-map-frame {
    height: 320px;
    background: #e5e7eb;
  }

  .kt-map-frame iframe {
    width: 100%;
    height: 100%;
    border: 0;
  }

  @media (max-width: 640px) {
    .kt-map-frame {
      height: 260px;
    }
     .kt-contact-page {
      padding: 40px 0px 0px 0px;
     }
  }
</style>

<section class="kt-contact-page">
  <div class="kt-contact-inner">

    <!-- Section 1: Breadcrumb + Heading -->
    <div class="kt-contact-breadcrumb">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <span><?php the_title(); ?></span>
    </div>
    <h1 class="kt-contact-heading"><?php the_title(); ?></h1>
    <p class="kt-contact-subtitle">
      We're always here to assist with your orders, product questions and custom heater or cosmetics inquiries.
    </p>

    <!-- Section 2: Main Panel -->
    <div class="kt-contact-main">

      <!-- LEFT: FORM -->
      <div>
        <h2 class="kt-form-title">We're Always Here To Assist</h2>
        <p class="kt-form-text">
          Fill in the form and our KachoTech support team will get back to you within one working day.
        </p>

        <form id="kt-contact-form" novalidate>
          <div class="kt-form-row">
            <div class="kt-field">
              <label for="kt-name">Your Name *</label>
              <input id="kt-name" name="name" type="text" class="kt-input" placeholder="Your Name">
            </div>
            <div class="kt-field">
              <label for="kt-phone">Mobile Number</label>
              <input id="kt-phone" name="phone" type="tel" class="kt-input" placeholder="+92 3XX XXX XXXX">
            </div>
          </div>

          <div class="kt-field">
            <label for="kt-email">Your Email Address *</label>
            <input id="kt-email" name="email" type="email" class="kt-input" placeholder="you@example.com">
          </div>

          <div class="kt-field" style="margin-top:12px;">
            <label for="kt-message">Additional Message *</label>
            <textarea id="kt-message" name="message" class="kt-textarea"
                      placeholder="Tell us about your query, order or project…"></textarea>
          </div>

          <div class="kt-form-footer">
            <label class="kt-checkbox">
              <input id="kt-terms" type="checkbox">
              <span>I agree with the <a href="#">terms &amp; conditions</a>.</span>
            </label>

            <button type="submit" id="kt-submit" class="kt-button" disabled>
              Submit
              <span class="kt-arrow">➜</span>
            </button>
          </div>

          <div id="kt-form-msg" class="kt-form-message"></div>
        </form>
      </div>

      <!-- MIDDLE: CONTACT INFO -->
      <div>
        <div class="kt-contact-info-block">
          <h3>Our Address</h3>
          <p>
            KachoTech Ahmed Center shop no 12 (LG),<br>
            Property # B 601–605, Nia Mohalla Street #1,<br>
            Rawalpindi, Pakistan.
          </p>
        </div>

        <div class="kt-contact-info-block">
          <h3>Contact Details</h3>
          <p>
            Phone: <a href="tel:+923080007082">+92 3080007 082</a><br>
            Email: <a href="mailto:support@kachotech.com">support@kachotech.com</a>
          </p>
        </div>

        <div class="kt-contact-info-block">
          <h3>Social Media</h3>
          <div class="kt-info-social">
            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
            <a href="#" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
          </div>
        </div>

        <div class="kt-contact-info-block">
          <h3>Opening Hours</h3>
          <p>
            Monday – Friday: 10:00 AM – 9:00 PM<br>
            Saturday &amp; Sunday: 12:00 PM – 8:00 PM
          </p>
        </div>
      </div>

      <!-- RIGHT: PHOTO -->
      <div class="kt-contact-photo">
        <img src="http://kachotech.com/wp-content/uploads/2025/12/contact-us-team-of-kachotech.jpg"
             alt="Friendly KachoTech support representative with headphones">
      </div>
    </div>

  </div>

  <!-- Section 3: Map band -->
  <div class="kt-contact-map">
    <div class="kt-map-frame">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1772.0761089773791!2d73.07282907598037!3d33.669201802553395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x38df954beef18773%3A0x5561640d321b0b04!2sAhmed%20Center!5e0!3m2!1sen!2s!4v1765297791850!5m2!1sen!2s"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade" title="Ahmed Center Location"></iframe>
    </div>
  </div>
</section>

<script>
  (function () {
    var form   = document.getElementById('kt-contact-form');
    var terms  = document.getElementById('kt-terms');
    var submit = document.getElementById('kt-submit');
    var msg    = document.getElementById('kt-form-msg');

    function toggleSubmit() {
      submit.disabled = !terms.checked;
    }

    terms.addEventListener('change', toggleSubmit);

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var nameEl    = document.getElementById('kt-name');
      var emailEl   = document.getElementById('kt-email');
      var messageEl = document.getElementById('kt-message');
      var phoneEl   = document.getElementById('kt-phone');

      var hasError = false;
      [nameEl, emailEl, messageEl].forEach(function (el) {
        el.classList.remove('kt-input-error');
      });

      if (!nameEl.value.trim()) {
        nameEl.classList.add('kt-input-error');
        hasError = true;
      }

      if (!emailEl.value.trim() || emailEl.value.indexOf('@') === -1) {
        emailEl.classList.add('kt-input-error');
        hasError = true;
      }

      if (!messageEl.value.trim()) {
        messageEl.classList.add('kt-input-error');
        hasError = true;
      }

      if (hasError) {
        msg.textContent = 'Please fill in the required fields highlighted above.';
        msg.className = 'kt-form-message kt-form-message--error';
        return;
      }

      msg.textContent = 'Sending your message…';
      msg.className = 'kt-form-message';

      var formData = new FormData();
      formData.append('action', 'kt_contact_form');
      formData.append('name', nameEl.value.trim());
      formData.append('email', emailEl.value.trim());
      formData.append('phone', phoneEl.value.trim());
      formData.append('message', messageEl.value.trim());

      fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
        method: 'POST',
        body: formData
      })
      .then(function (response) { return response.json(); })
      .then(function (data) {
        if (data && data.success) {
          msg.textContent = (data.data && data.data.message)
            ? data.data.message
            : 'Thank you! Your message has been sent.';
          msg.className = 'kt-form-message kt-form-message--success';
          form.reset();
          toggleSubmit();
        } else {
          msg.textContent = (data && data.data && data.data.message)
            ? data.data.message
            : 'Sorry, something went wrong. Please try again later.';
          msg.className = 'kt-form-message kt-form-message--error';
        }
      })
      .catch(function () {
        msg.textContent = 'Network error. Please try again.';
        msg.className = 'kt-form-message kt-form-message--error';
      });
    });
  })();
</script>

<?php get_footer(); ?>
