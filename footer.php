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
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
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
</script>

</body>
</html>