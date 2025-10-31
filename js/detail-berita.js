// ===== SLIDER GAMBAR UTAMA =====
let currentSlideIndex = 0;
let slides = [];
let dots = [];
let totalSlides = 0;

// Inisialisasi slider
function initSlider() {
    slides = document.querySelectorAll('.slide');
    dots = document.querySelectorAll('.dot');
    totalSlides = slides.length;
    
    if (totalSlides > 0) {
        showSlide(currentSlideIndex);
    }
}

// Fungsi untuk mengubah slide
function showSlide(n) {
    // Reset semua slide
    slides.forEach(slide => {
        slide.classList.remove('active');
    });
    
    // Reset semua dots
    dots.forEach(dot => {
        dot.classList.remove('active');
    });
    
    // Handle infinite loop
    if (n >= totalSlides) {
        currentSlideIndex = 0;
    } else if (n < 0) {
        currentSlideIndex = totalSlides - 1;
    } else {
        currentSlideIndex = n;
    }
    
    // Tampilkan slide aktif
    slides[currentSlideIndex].classList.add('active');
    
    // Aktifkan dot yang sesuai
    if (dots.length > 0 && dots[currentSlideIndex]) {
        dots[currentSlideIndex].classList.add('active');
    }
}

// Fungsi untuk next/previous slide
function changeSlide(n) {
    showSlide(currentSlideIndex + n);
}

// Fungsi untuk langsung ke slide tertentu
function goToSlide(n) {
    showSlide(n);
}

// Auto slide (opsional - bisa diaktifkan jika diinginkan)
function autoSlide() {
    if (totalSlides > 1) {
        changeSlide(1);
    }
}

// ===== MODAL GAMBAR =====
function openModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    
    if (modal && modalImg) {
        modal.style.display = 'block';
        modalImg.src = 'img/berita/' + imageSrc;
        
        // Tambahkan event listener untuk ESC key
        document.addEventListener('keydown', handleKeyDown);
    }
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.style.display = 'none';
        
        // Hapus event listener
        document.removeEventListener('keydown', handleKeyDown);
    }
}

// Fungsi untuk handle keyboard events
function handleKeyDown(e) {
    if (e.key === 'Escape') {
        closeModal();
    } else if (e.key === 'ArrowLeft') {
        changeSlide(-1);
    } else if (e.key === 'ArrowRight') {
        changeSlide(1);
    }
}

// ===== EVENT LISTENERS =====
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi slider
    initSlider();
    
    // Auto slide setiap 5 detik (opsional)
    // if (totalSlides > 1) {
    //     setInterval(autoSlide, 5000);
    // }
    
    // Event listener untuk klik di luar modal
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
    
    // Event listener untuk keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft' && totalSlides > 1) {
            changeSlide(-1);
        } else if (e.key === 'ArrowRight' && totalSlides > 1) {
            changeSlide(1);
        }
    });
    
    // Touch events untuk mobile swipe
    let startX = 0;
    let endX = 0;
    
    const slider = document.getElementById('imageSlider');
    if (slider && totalSlides > 1) {
        slider.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
        });
        
        slider.addEventListener('touchend', function(e) {
            endX = e.changedTouches[0].clientX;
            handleSwipe();
        });
    }
    
    function handleSwipe() {
        const swipeThreshold = 50;
        
        if (startX - endX > swipeThreshold) {
            // Swipe kiri - next slide
            changeSlide(1);
        } else if (endX - startX > swipeThreshold) {
            // Swipe kanan - previous slide
            changeSlide(-1);
        }
    }
});

// ===== FITUR TAMBAHAN: LAZY LOADING =====
// Untuk optimasi performa gambar
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.getAttribute('data-src');
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Panggil lazy loading setelah DOM loaded
document.addEventListener('DOMContentLoaded', lazyLoadImages);