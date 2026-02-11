/**
 * BY Production - Portfolio Website
 * JavaScript for animations and interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    // ===== Admin API Integration =====
    const API_BASE = document.body.dataset.api || 'http://localhost:8000/admin/api/public.php';

    // Load dynamic content from Admin backend
    loadCollaborations();
    loadAboutContent();

    async function loadCollaborations() {
        const grid = document.getElementById('collabGrid');
        const loading = document.getElementById('collabLoading');
        if (!grid) return;

        try {
            const res = await fetch(`${API_BASE}?action=get_collaborations`);
            const data = await res.json();

            if (data.success && data.collaborations.length > 0) {
                if (loading) loading.remove();

                data.collaborations.forEach((collab, index) => {
                    const card = document.createElement('div');
                    card.className = 'collab-card' + (collab.is_featured == 1 ? ' collab-card--large' : '');

                    // Use first image or fallback
                    const imgUrl = collab.thumbnail
                        ? `${API_BASE.replace('/admin/api/public.php', '/admin/')}${collab.thumbnail}`
                        : 'assets/tennis.png';

                    card.innerHTML = `
                        <div class="collab-logo">
                            <span class="collab-name-label">${escapeHtml(collab.name)}</span>
                        </div>
                        <div class="collab-image">
                            <img src="${imgUrl}" alt="${escapeHtml(collab.name)}">
                        </div>
                    `;

                    // Add click handler for detail view (video, description)
                    if (collab.video_url || collab.description) {
                        card.style.cursor = 'pointer';
                        card.addEventListener('click', () => showCollabDetail(collab));
                    }

                    grid.appendChild(card);

                    // Animate in with stagger
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(40px)';
                    card.style.transition = `opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1) ${index * 0.1}s, transform 0.8s cubic-bezier(0.16, 1, 0.3, 1) ${index * 0.1}s`;
                    fadeInObserver.observe(card);
                });
            } else if (data.success && data.collaborations.length === 0) {
                if (loading) loading.textContent = 'Noch keine Kollaborationen vorhanden.';
            }
        } catch (e) {
            console.warn('Could not load collaborations from admin:', e);
            if (loading) loading.textContent = '';
        }
    }

    async function loadAboutContent() {
        try {
            const res = await fetch(`${API_BASE}?action=get_cms_section&slug=about-me`);
            const data = await res.json();

            if (data.success && data.section) {
                const section = data.section;

                // If content is JSON with structured fields
                if (section.content_data) {
                    const d = section.content_data;
                    if (d.name) {
                        const nameEl = document.getElementById('aboutName');
                        if (nameEl) nameEl.textContent = d.name;
                    }
                    if (d.text) {
                        const textEl = document.getElementById('aboutText');
                        if (textEl) textEl.textContent = d.text;
                    }
                    if (d.portrait_url) {
                        const portrait = document.getElementById('aboutPortrait');
                        if (portrait) portrait.src = d.portrait_url;
                    }
                    if (d.image_url) {
                        const aboutImg = document.getElementById('aboutImage');
                        if (aboutImg) aboutImg.src = d.image_url;
                    }
                    if (d.stats && Array.isArray(d.stats)) {
                        const statsContainer = document.getElementById('statsContent');
                        if (statsContainer) {
                            statsContainer.innerHTML = d.stats.map(s => `
                                <div class="stat">
                                    <span class="stat-number">${escapeHtml(s.number)}</span>
                                    <span class="stat-label">${escapeHtml(s.label)}</span>
                                </div>
                            `).join('');
                        }
                    }
                } else if (section.content) {
                    // Plain HTML content — inject into the about text
                    const textEl = document.getElementById('aboutText');
                    if (textEl) textEl.innerHTML = section.content;
                }
            }
        } catch (e) {
            console.warn('Could not load About Me from CMS:', e);
            // Fallback: keep static content
        }
    }

    function showCollabDetail(collab) {
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.className = 'collab-detail-overlay';

        let videoHtml = '';
        if (collab.video_url) {
            const ytMatch = collab.video_url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
            const vimeoMatch = collab.video_url.match(/vimeo\.com\/(\d+)/);
            if (ytMatch) {
                videoHtml = `<iframe src="https://www.youtube.com/embed/${ytMatch[1]}" width="100%" height="400" frameborder="0" allowfullscreen style="border-radius:12px;margin-top:20px;"></iframe>`;
            } else if (vimeoMatch) {
                videoHtml = `<iframe src="https://player.vimeo.com/video/${vimeoMatch[1]}" width="100%" height="400" frameborder="0" allowfullscreen style="border-radius:12px;margin-top:20px;"></iframe>`;
            }
        }

        // Build image gallery
        let galleryHtml = '';
        if (collab.images && collab.images.length > 0) {
            const galleryItems = collab.images.map(img => {
                const src = `${API_BASE.replace('/admin/api/public.php', '/admin/')}${img.image_url}`;
                return `<img src="${src}" alt="${escapeHtml(img.alt_text || collab.name)}" style="width:100%;border-radius:12px;margin-top:12px;">`;
            }).join('');
            galleryHtml = `<div class="collab-detail-gallery">${galleryItems}</div>`;
        }

        overlay.innerHTML = `
            <div class="collab-detail-modal">
                <button class="collab-detail-close">✕</button>
                <h2 class="collab-detail-title">${escapeHtml(collab.name)}</h2>
                ${collab.short_description ? `<p class="collab-detail-subtitle">${escapeHtml(collab.short_description)}</p>` : ''}
                ${videoHtml}
                ${collab.description ? `<p class="collab-detail-desc">${escapeHtml(collab.description)}</p>` : ''}
                ${galleryHtml}
            </div>
        `;

        // Close handlers
        const closeModal = () => {
            overlay.remove();
            document.body.style.overflow = '';
            document.removeEventListener('keydown', escHandler);
        };
        const escHandler = (e) => { if (e.key === 'Escape') closeModal(); };

        overlay.querySelector('.collab-detail-close').addEventListener('click', closeModal);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });
        document.addEventListener('keydown', escHandler);

        // Lock body scroll
        document.body.style.overflow = 'hidden';
        document.body.appendChild(overlay);
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text || '';
        return d.innerHTML;
    }

    const hero = document.getElementById('hero');
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

    // ===== Hero Animation =====
    // Trigger animation after 3 seconds
    setTimeout(() => {
        hero.classList.add('animated');
    }, 3000);

    // ===== Mobile Navigation =====
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        mobileNav.classList.toggle('active');
        document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
    });

    // Close mobile nav when clicking a link
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            mobileNav.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // ===== Smooth Scroll =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const headerHeight = document.getElementById('header').offsetHeight;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ===== Scroll Animations with Staggered Delays =====
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const fadeInObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    // Observe elements for scroll animations with staggered delays
    const animatedElements = document.querySelectorAll('.about-card, .about-image, .stat, .collab-card');
    animatedElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(40px)';
        el.style.transition = `opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1) ${index * 0.1}s, transform 0.8s cubic-bezier(0.16, 1, 0.3, 1) ${index * 0.1}s`;
        fadeInObserver.observe(el);
    });

    // Add CSS for visible state
    const style = document.createElement('style');
    style.textContent = `
        .visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        
        /* Smooth image loading */
        img {
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        
        img.loaded {
            opacity: 1;
        }
    `;
    document.head.appendChild(style);

    // ===== Smooth Image Loading =====
    document.querySelectorAll('img').forEach(img => {
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', () => img.classList.add('loaded'));
        }
    });

    // ===== Parallax Effect on Hero =====
    const heroGlow = document.querySelector('.hero-glow');
    const heroLogo = document.getElementById('heroLogo');

    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        const heroHeight = hero.offsetHeight;

        if (scrollY < heroHeight) {
            const parallaxValue = scrollY * 0.3;
            if (heroGlow) {
                heroGlow.style.transform = `translateY(calc(-50% + ${parallaxValue}px))`;
            }
            if (heroLogo && !hero.classList.contains('animated')) {
                heroLogo.style.transform = `translateY(${parallaxValue * 0.2}px)`;
            }
        }
    });

    // ===== Stats Counter Animation =====
    let statsAnimated = false;

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !statsAnimated) {
                statsAnimated = true;
                animateStats();
            }
        });
    }, { threshold: 0.5 });

    const statsSection = document.querySelector('.stats');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }

    function animateStats() {
        // Re-query fresh elements (CMS may have replaced them)
        const freshStatNumbers = document.querySelectorAll('.stat-number');
        freshStatNumbers.forEach(stat => {
            const text = stat.textContent;
            const number = parseInt(text);
            if (isNaN(number)) { return; } // skip non-numeric stats (e.g. "100%")
            const suffix = text.replace(/[0-9]/g, '');

            let current = 0;
            const increment = Math.max(number / 50, 1);
            const duration = 2000;
            const stepTime = duration / 50;

            const counter = setInterval(() => {
                current += increment;
                if (current >= number) {
                    stat.textContent = text;
                    clearInterval(counter);
                } else {
                    stat.textContent = Math.floor(current) + suffix;
                }
            }, stepTime);
        });
    }

    // ===== Header Background on Scroll =====
    const header = document.getElementById('header');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.style.background = 'rgba(0, 0, 0, 0.9)';
            header.style.backdropFilter = 'blur(10px)';
        } else {
            header.style.background = 'transparent';
            header.style.backdropFilter = 'none';
        }
    });
});
