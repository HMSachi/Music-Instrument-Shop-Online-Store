    </main>
    
    <footer class="main-footer" style="background: hsla(330, 20%, 10%, 0.95); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-top: 1px solid hsla(0, 0%, 100%, 0.1); padding: 8rem 0 4rem; color: #fff; position: relative; overflow: hidden;">
        <!-- Background Glow -->
        <div style="position: absolute; top: -100px; right: -100px; width: 400px; height: 400px; background: radial-gradient(circle, hsla(var(--p-h), 83%, 53%, 0.05) 0%, transparent 70%); pointer-events: none;"></div>
        
        <div class="container">
            <div class="footer-content" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 4rem; margin-bottom: 6rem;">
                <div class="footer-section">
                    <h3 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 2rem; color: #fff; letter-spacing: -1px;">
                        <i class="fas fa-music" style="color: var(--primary);"></i> Melody Masters
                    </h3>
                    <p style="color: hsla(0, 0%, 100%, 0.6); line-height: 1.8; margin-bottom: 2.5rem; font-size: 1.05rem;">
                        Elevating your musical journey since 2024. Your premier destination for world-class instruments, professional gear, and expert guidance.
                    </p>
                    <div class="social-links" style="display: flex; gap: 1.25rem;">
                        <?php 
                        $socials = ['facebook-f', 'twitter', 'instagram', 'youtube'];
                        foreach($socials as $s): 
                        ?>
                            <a href="#" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: hsla(0, 0%, 100%, 0.05); border: 1px solid hsla(0, 0%, 100%, 0.1); border-radius: 50%; color: #fff; transition: all 0.3s ease;">
                                <i class="fab fa-<?php echo $s; ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 2rem; color: #fff; text-transform: uppercase; letter-spacing: 2px;">Explore</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 1.25rem;"><a href="<?php echo SITE_URL; ?>/index.php" style="color: hsla(0, 0%, 100%, 0.6); text-decoration: none; transition: color 0.3s ease;">Home Showcase</a></li>
                        <li style="margin-bottom: 1.25rem;"><a href="<?php echo SITE_URL; ?>/shop.php" style="color: hsla(0, 0%, 100%, 0.6); text-decoration: none; transition: color 0.3s ease;">Instrument Catalog</a></li>
                        <li style="margin-bottom: 1.25rem;"><a href="<?php echo SITE_URL; ?>/cart.php" style="color: hsla(0, 0%, 100%, 0.6); text-decoration: none; transition: color 0.3s ease;">Shopping Cart</a></li>
                        <li style="margin-bottom: 1.25rem;"><a href="#" style="color: hsla(0, 0%, 100%, 0.6); text-decoration: none; transition: color 0.3s ease;">Featured Brands</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 2rem; color: #fff; text-transform: uppercase; letter-spacing: 2px;">Support</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 1.25rem;"><a href="#" style="color: hsla(0, 0%, 100%, 0.6); text-decoration: none; transition: color 0.3s ease;">Knowledge Base</a></li>
                        <li style="margin-bottom: 1.25rem;"><a href="#" style="color: hsla(0, 0%, 100%, 0.6); text-decoration: none; transition: color 0.3s ease;">Contact Concierge</a></li>
                        <li style="margin-bottom: 1.25rem;"><a href="#" style="color: hsla(0, 0%, 100%, 0.6); text-decoration: none; transition: color 0.3s ease;">Shipping Logistics</a></li>
                        <li style="margin-bottom: 1.25rem;"><a href="#" style="color: hsla(0, 0%, 100%, 0.6); text-decoration: none; transition: color 0.3s ease;">Return Satisfaction</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 2rem; color: #fff; text-transform: uppercase; letter-spacing: 2px;">Contact</h4>
                    <ul style="list-style: none; padding: 0; color: hsla(0, 0%, 100%, 0.6);">
                        <li style="margin-bottom: 1.5rem; display: flex; gap: 1rem;"><i class="fas fa-map-marker-alt" style="color: var(--primary); margin-top: 5px;"></i> 123 Music Ave, Studio District, QC</li>
                        <li style="margin-bottom: 1.5rem; display: flex; gap: 1rem;"><i class="fas fa-phone" style="color: var(--primary); margin-top: 5px;"></i> +63 912 345 6789</li>
                        <li style="margin-bottom: 1.5rem; display: flex; gap: 1rem;"><i class="fas fa-envelope" style="color: var(--primary); margin-top: 5px;"></i> hello@melodymasters.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom" style="border-top: 1px solid hsla(0, 0%, 100%, 0.05); padding-top: 3rem; display: flex; justify-content: space-between; align-items: center;">
                <p style="color: hsla(0, 0%, 100%, 0.4); font-size: 0.9rem;">&copy; <?php echo date('Y'); ?> Melody Masters. All rights reserved. Precision-tuned excellence.</p>
                <div class="payment-methods" style="display: flex; gap: 1.5rem; font-size: 1.5rem; color: hsla(0, 0%, 100%, 0.2);">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fab fa-cc-apple-pay"></i>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
