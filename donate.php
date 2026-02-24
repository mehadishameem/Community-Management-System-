<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Donate</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap');

    #content {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding-top: 10px; /* 10px gap below navbar */
      min-height: calc(100vh - 90px);
      background:rgb(255, 255, 255); /* lighter cyan for better contrast */
      padding-left: 20px;
      padding-right: 20px;
      box-sizing: border-box;
    }

    .donate-box {
      background: rgba(255, 255, 255, 0.25); /* slightly more opaque white */
      border-radius: 25px;
      padding: 30px 40px;
      width: 100%;
      max-width: 480px;
      text-align: center;
      color: #e9f5ff; /* brighter pale blue */
      font-family: 'Poppins', sans-serif;
      box-shadow:
        0 8px 32px 0 rgba(20, 35, 75, 0.8); /* darker shadow for better definition */
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.35);
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.8s ease forwards;
    }

    @keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(40px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .donate-box::before {
      content: '';
      position: absolute;
      top: -60%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, #5279cc, #2e4b87, #1f3660);
      animation: rotateBg 12s linear infinite;
      filter: blur(55px);
      opacity: 0.85;
      z-index: 0;
      border-radius: 50%;
    }

    @keyframes rotateBg {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .donate-box * {
      position: relative;
      z-index: 1;
    }

    /* Logos container side by side */
    .logo-row {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-bottom: 30px;
    }
    .logo-row img {
      width: 100px;
      cursor: pointer;
      filter: drop-shadow(0 0 15px #5279cc);
      transition: transform 0.3s ease;
    }
    .logo-row img:hover {
      transform: scale(1.15);
    }

    .donate-box h1 {
      font-weight: 700;
      font-size: 2.8rem;
      margin-bottom: 20px;
      letter-spacing: 1.5px;
      text-shadow: 0 0 20px #5279cc;
      color: #dbe9ff;
    }

    .donate-box p {
      font-size: 1.2rem;
      line-height: 1.6;
      margin-bottom: 2rem;
      color: #d7e8ffdd;
    }

    .donate-box strong {
      color: #6b95ff;
    }

    .bkash-number, .bank-details {
      font-size: 1.4rem;
      font-weight: 900;
      letter-spacing: 4px;
      color:rgb(255, 255, 255);
      padding: 18px 40px;
      border: 3px solid #6b95ff;
      border-radius: 50px;
      margin: 0 auto 35px auto;
      cursor: pointer;
      box-shadow:
        0 0 18px #6b95ff,
        0 0 35px #6b95ff,
        0 0 65px #6b95ff;
      transition: box-shadow 0.3s ease, background-color 0.3s ease, color 0.3s ease;
      user-select: all;
      max-width: fit-content;
    }
    .bkash-number:hover, .bank-details:hover {
      background-color: #6b95ff;
      color: #0a1c3d;
      box-shadow:
        0 0 35px #94afff,
        0 0 55px #94afff,
        0 0 100px #94afff;
    }

    .bank-details p {
      margin: 4px 0;
      font-weight: 700;
      letter-spacing: normal;
      color:rgb(255, 255, 255);
    }

    .thank-you {
      font-size: 1.1rem;
      font-style: italic;
      color: #d1dfffdd;
    }
  </style>
</head>
<body>
  <div id="content">
    <div class="donate-box">
      <div class="logo-row">
        <img
          src="bkash.png"
          alt="bKash Logo"
          onclick="copyBkashNumber()"
          title="ক্লিক করে বিকাশ নম্বর কপি করুন"
        />
        <img
          src="sonali.png"
          alt="Bank Logo"
          onclick="copyBankAccount()"
          title="ক্লিক করে ব্যাংক একাউন্ট নম্বর কপি করুন"
        />
      </div>
     
       <p>
    মোবাইল ব্যাংকিং অথবা ব্যাংকের মাধ্যমে<br>
    অনুদান পাঠাতে চাইলে,অনুগ্রহ করে নিচের<br>
    <strong>বিকাশ/ব্যাংক নম্বর</strong> ব্যবহার করুন।<br>
    
    </p>

      <div class="bkash-number" onclick="copyBkashNumber()" title="ক্লিক করে বিকাশ নম্বর কপি করুন">
        ০১৬৪৭০৫২১৬৮
      </div>
      
      <div class="bank-details" onclick="copyBankAccount()" title="ক্লিক করে ব্যাংক একাউন্ট নম্বর কপি করুন">
        <p>Islami Bank PLC</p>
        <p>Account: 20507770204046625</p>
      </div>

      <p class="thank-you">ধন্যবাদ আপনার সহযোগিতার জন্য!</p>
    </div>
  </div>

  <script>
    function copyBkashNumber() {
      const number = '+880 1647 052168';
      navigator.clipboard.writeText(number).then(() => {
        alert('বিকাশ নম্বর কপি হয়েছে: ' + number);
      });
    }

    function copyBankAccount() {
      const account = '20507770204046625';
      navigator.clipboard.writeText(account).then(() => {
        alert('ব্যাংক একাউন্ট নম্বর কপি হয়েছে: ' + account);
      });
    }
  </script>
</body>
</html>
