  <footer class="footer">
    <div class="footer-main">
      <div class="footer-brand">Naxa</div>
      <div class="footer-slogan">Be the axis of innovation.</div>

      <a href="https://open.kakao.com/o/sjxp87Af" target="_blank" class="cta-button">
        Let's build together →
      </a>

      <div class="social-links">
        <a href="https://github.com/seunghwan94" target="_blank" class="social-link">
           <i class="fab fa-github social-icon" aria-hidden="true"></i>
          <span>GitHub</span>
        </a>
        <a href="mailto:sounghan94@gmail.com" class="social-link">
          <i class="fa-solid fa-envelope"></i>
          <span>Email</span>
        </a>
        <a href="https://open.kakao.com/o/sjxp87Af" target="_blank" class="social-link">
          <i class="fa-solid fa-comment-dots"></i>
          <span>OpenChat</span>
        </a>
        <button onclick="openDonationModal()" class="social-link donation-btn">
          <i class="fa-solid fa-heart"></i>
          <span>후원하기</span>
        </button>
        <div class="social-link" style="cursor: default; opacity: 0.5;">
          <i class="fa-brands fa-instagram"></i>
          <span>Instagram (Coming Soon)</span>
        </div>
      </div>

      <div class="footer-info">
        <div class="update-date">
          <i class="fa-solid fa-clock social-icon" aria-hidden="true"></i>
          <span>최근 업데이트: 2025.06.23</span>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="footer-bottom-content">
        © 2025 Naxa.dev · Crafted by Naxa · All rights reserved.
      </div>
    </div>
  </footer>

  <button class="back-to-top" onclick="scrollToTop()" aria-label="Back to top">
    ↑
  </button>

  <div class="modal-overlay" id="donationModal" onclick="closeDonationModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
      <button class="modal-close" onclick="closeDonationModal()">&times;</button>
      <h3 class="modal-title">💝 후원하기</h3>

      <div class="modal-qr">
        <img src="static/img/logo.png" alt="후원 QR코드">
      </div>

      <div class="account-info">
        <div class="account-bank">카카오뱅크</div>
        <div class="account-number" onclick="copyAccountNumber()" title="클릭하여 복사">
          3333-25-7299074
        </div>
        <div class="account-name">이승환</div>
      </div>

      <div class="copy-notice">계좌번호를 클릭하면 복사됩니다</div>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
    // Donation Modal Functions
    function openDonationModal() {
      document.getElementById('donationModal').classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    function closeDonationModal(event) {
      if (!event || event.target === event.currentTarget) {
        document.getElementById('donationModal').classList.remove('active');
        document.body.style.overflow = 'auto';
      }
    }

    function copyAccountNumber() {
      const accountNumber = '3333-25-7299074';
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(accountNumber).then(() => {
          showToast('계좌번호가 복사되었습니다! 🎉');
        }).catch(() => { fallbackCopy(accountNumber); });
      } else {
        fallbackCopy(accountNumber);
      }
    }
    function fallbackCopy(text) {
      const ta = document.createElement('textarea');
      ta.value = text;
      ta.style.position = 'fixed';
      ta.style.left = '-999999px';
      ta.style.top = '-999999px';
      document.body.appendChild(ta);
      ta.focus();
      ta.select();
      try {
        document.execCommand('copy');
        showToast('계좌번호가 복사되었습니다! 🎉');
      } catch {
        showToast('복사에 실패했습니다. 수동으로 복사해주세요.');
      }
      document.body.removeChild(ta);
    }
    function showToast(message) {
      const toast = document.getElementById('toast');
      toast.textContent = message;
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // Back to Top
    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    window.addEventListener('scroll', () => {
      document.querySelector('.back-to-top')
        .classList.toggle('visible', window.pageYOffset > 300);
    });

    // Scroll Animations
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => e.isIntersecting && e.target.classList.add('animate'));
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    document.querySelectorAll('.card').forEach(c => observer.observe(c));

    // Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDonationModal(); });
  </script>
</body>
</html>
