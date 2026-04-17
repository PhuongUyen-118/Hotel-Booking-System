<?php
// contact.php
include 'includes/header.php'; 
session_start();
// Xử lý gửi liên hệ ngay trong file
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    $to = "support@hotelbooking.com"; // email nhận
    $headers = "From: $email\r\nReply-To: $email\r\n";
    $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";

    if (mail($to, $subject, $body, $headers)) {
        $success = "Your message has been sent successfully!";
    } else {
        $error = "Error sending message. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hotel Booking</title>
    <link rel="stylesheet" href="assets/image/css/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="contact-container">
    <div class="contact-info">
        <h2>Contact Us</h2>
        <p>We’re here to help you 24/7. Reach out to us anytime.</p>
        <div class="info-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>123 Nguyen Hue, District 1, Ho Chi Minh City</span>
        </div>
        <div class="info-item">
            <i class="fas fa-phone"></i>
            <span>+84 123 456 789</span>
        </div>
        <div class="info-item">
            <i class="fas fa-envelope"></i>
            <span>support@hotelbooking.com</span>
        </div>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-tiktok"></i></a>
        </div>
    </div>

    <div class="contact-form">
        <h2>Send a Message</h2>
        <form action="send_contact.php" method="POST">
            <div class="form-group">
                <input type="text" name="name" placeholder="Your Name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Your Email" required>
            </div>
            <div class="form-group">
                <input type="text" name="subject" placeholder="Subject" required>
            </div>
            <div class="form-group">
                <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
            </div>
            <button type="submit">Send Now</button>
        </form>
    </div>
</div>

<!-- FAQ Section -->
<div class="faq-section">
    <h2>Frequently Asked Questions</h2>

    <div class="faq-item">
        <div class="faq-question">
            <span>How can I cancel my booking?</span>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="faq-answer">
            You can cancel your booking by logging into your account, going to "My Bookings", and selecting "Cancel" next to the reservation.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Do you offer refunds?</span>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="faq-answer">
            Refunds depend on the hotel's cancellation policy. Some bookings are non-refundable, while others allow partial or full refunds.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Can I change my booking dates?</span>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="faq-answer">
            Yes, you can request a change of dates through your account or by contacting our support team, subject to hotel availability.
        </div>
    </div>
</div>

<script>
    // FAQ toggle
    document.querySelectorAll('.faq-question').forEach(item => {
        item.addEventListener('click', () => {
            item.classList.toggle('active');
            let answer = item.nextElementSibling;
            answer.style.display = answer.style.display === 'block' ? 'none' : 'block';
        });
    });
</script>

</body>
</html>
