document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.flagship-site-header');
    
    if (!header) return;

    let ticking = false;

    const onScroll = () => {
        if (window.scrollY > 50) {
            header.classList.add('is-scrolled');
        } else {
            header.classList.remove('is-scrolled');
        }
        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(onScroll);
            ticking = true;
        }
    });
});
