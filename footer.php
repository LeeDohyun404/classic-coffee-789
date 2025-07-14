<style>
/* ================================== */
/* FOOTER STYLING                     */
/* ================================== */
footer {
    background-color: #333;
    color: #f4f4f4;
    padding: 40px 20px;
    margin-top: 50px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.footer-content {
    display: flex;
    justify-content: center;
    text-align: center;
    max-width: 1100px;
    margin: auto;
}

.footer-section {
    flex: 1;
    max-width: 400px;
}

.footer-section h3 {
    color: #FFE4B5;
    font-family: 'Playfair Display', serif;
    font-size: 1.8em;
    margin-bottom: 20px;
    font-weight: bold;
}

.footer-section p {
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 20px;
    color: #cccccc;
}

.footer-section .contact {
    margin-bottom: 20px;
}

.footer-section .contact span {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    color: #f4f4f4;
}

.footer-section .contact span i {
    margin-right: 8px;
    color: #FFE4B5;
    width: 16px;
    text-align: center;
}

.footer-section .socials {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

.footer-section .socials a {
    color: #f4f4f4;
    font-size: 1.5em;
    text-decoration: none;
    transition: color 0.3s ease, transform 0.3s ease;
    display: inline-block;
}

.footer-section .socials a:hover {
    color: #FFE4B5;
    transform: translateY(-2px);
}

.footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #555;
    margin-top: 20px;
    font-size: 14px;
    color: #cccccc;
}

/* Mobile Responsive Footer */
@media (max-width: 768px) {
    footer {
        padding: 30px 15px;
    }
    
    .footer-content {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-section {
        margin-bottom: 30px;
    }
    
    .footer-section h3 {
        font-size: 1.5em;
        margin-bottom: 15px;
    }
    
    .footer-section p {
        font-size: 13px;
    }
    
    .footer-section .contact span {
        font-size: 13px;
        margin-bottom: 6px;
    }
    
    .footer-section .socials {
        gap: 20px;
    }
    
    .footer-section .socials a {
        font-size: 1.8em;
    }
    
    .footer-bottom {
        font-size: 13px;
        padding-top: 15px;
        margin-top: 15px;
    }
}

@media (max-width: 480px) {
    .footer-section h3 {
        font-size: 1.3em;
    }
    
    .footer-section p {
        font-size: 12px;
    }
    
    .footer-section .contact span {
        font-size: 12px;
    }
    
    .footer-section .socials a {
        font-size: 1.6em;
    }
    
    .footer-bottom {
        font-size: 12px;
    }
}
</style>

<footer>
    <div class="footer-content">
        <div class="footer-section about">
            <h3 class="logo-text">Classic Coffee 789</h3>
            <p>
                Menyajikan kehangatan dalam setiap cangkir. Kunjungi kami untuk pengalaman kopi yang tak terlupakan.
            </p>
            <div class="contact">
                <span><i class="fas fa-map-marker-alt"></i> &nbsp; Jl. Kopi Nikmat No. 123, Jakarta</span>
                <span><i class="fas fa-phone"></i> &nbsp; +62 812-3456-7890</span>
            </div>
            <div class="socials">
                <a href="#" title="Instagram" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" title="WhatsApp" aria-label="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="#" title="TikTok" aria-label="TikTok">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?php echo date('Y'); ?> classiccoffee789.com | Designed with ☕
    </div>
</footer>

<script>
function changeQuantity(button, amount) {
    const quantityContainer = button.parentElement;
    const quantityInput = quantityContainer.querySelector('.quantity-input');
    let currentValue = parseInt(quantityInput.value);
    let newValue = currentValue + amount;

    if (newValue < 1) {
        newValue = 1;
    }
    
    quantityInput.value = newValue;
}

// Tambahan script untuk handling mobile menu jika diperlukan
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling untuk link anchor
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Loading animation untuk gambar
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
    });
});
</script>

</body>
</html>