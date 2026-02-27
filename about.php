<style>
  /* Center the whole content area with some padding */
  #content {
    display: flex;
    flex-direction: column;
    justify-content: center; /* vertical center */
    align-items: center;     /* horizontal center */
    min-height: calc(100vh - 80px); /* full viewport height minus navbar height */
    padding: 20px;
    box-sizing: border-box;
    background:rgb(255, 255, 255); /* light airy background */
  }

  /* Container holding the three info boxes */
  .container-contact {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
    max-width: 900px;
    width: 100%;
    margin-top: 40px;
  }

  /* Each info box */
  .box {
    background: white;
    flex: 1 1 280px;
    max-width: 280px;
    padding: 25px 20px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: default;
  }
  .box:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
  }

  /* Icon circles */
  .icon {
    font-size: 48px;
    color: white;
    width: 70px;
    height: 70px;
    line-height: 70px;
    border-radius: 50%;
    margin: 0 auto 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
  .icon-phone {
    background-color: #28a745; /* green */
  }
  .icon-email {
    background-color: #fd7e14; /* orange */
  }
  .icon-location {
    background-color: #007bff; /* blue */
  }

  /* Titles inside boxes */
  .box strong {
    font-size: 1.3rem;
    display: block;
    margin-bottom: 12px;
    color: #333;
  }

  /* Note below boxes */
  .note {
    max-width: 700px;
    margin: 40px auto 10px;
    font-size: 1.1rem;
    color: #555;
    text-align: center;
    font-style: italic;
  }

  /* Footer styling */
  .footer-contact {
    max-width: 700px;
    margin: 0 auto 30px;
    padding: 15px 10px;
    font-size: 14px;
    color: #666;
    text-align: center;
    border-top: 1px solid #ddd;
  }
  .footer-contact a {
    color: #444;
    text-decoration: none;
    margin: 0 8px;
    font-weight: 600;
  }
  .footer-contact a:hover {
    text-decoration: underline;
  }

  /* Heading */
  h2 {
    font-weight: 700;
    font-size: 2.4rem;
    color: #222;
  }

  /* Responsive for smaller screens */
  @media (max-width: 650px) {
    .container-contact {
      flex-direction: column;
      align-items: center;
    }
    .box {
      max-width: 90%;
    }
  }
</style>

<h2>যোগাযোগ</h2>

<div class="container-contact">
  <div class="box">
    <div class="icon icon-phone">📞</div>
    <strong>মোবাইল</strong>
    01959564847 (Shameem)<br />
    01910147518 (Motalib)<br />
    01568914535 (Yousuf)<br>
    01829113968 (sojol)
  </div>
  <div class="box">
    <div class="icon icon-email">✉️</div>
    <strong>ইমেইল</strong>
    srshamim0.23@gmail.com
  </div>
  <div class="box">
    <div class="icon icon-location">📍</div>
    <strong>ঠিকানা</strong>
    ইউসুফ আলী মুন্সি বাড়ি, গুনাইঘর উত্তর-৩৫৩০, দেবিদ্ধার কুমিল্লা
  </div>
</div>

<div class="note">
  সকাল ১০টা থেকে বিকাল ৫টা পর্যন্ত ফাউন্ডেশন সম্পর্কিত যেকোন গুরুত্বপূর্ণ তথ্য জানার জন্য প্রাপ্ত নাম্বারে কল করুন।
</div>

<div class="footer-contact">
  &copy; ২০২৫ | All rights reserved. | Developed by <a href="https://www.facebook.com/shamim0.25.inf/" target="_blank"><strong>Mehadi Hasan Shameem</strong></a><br />
  <a href="#">About Us</a> |
  <a href="#">Contact</a> |
  <a href="#">Terms and Conditions</a>
</div>
