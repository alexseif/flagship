document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('[data-counter-target]');
    
    if (!counters.length) return;

    const animateCounter = (counter) => {
        const target = +counter.getAttribute('data-counter-target');
        const duration = 2000;
        const stepTime = Math.abs(Math.floor(duration / target));
        let current = 0;
        
        const timer = setInterval(() => {
            current += Math.ceil(target / 100) || 1;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = current;
            }
        }, stepTime);
    };

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                obs.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.5
    });

    counters.forEach(counter => {
        observer.observe(counter);
    });
});
