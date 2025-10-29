// PDF Viewer Controls
document.addEventListener('DOMContentLoaded', function() {
    const pdfFrame = document.getElementById('pdf-frame');
    const zoomInBtn = document.getElementById('zoom-in');
    const zoomOutBtn = document.getElementById('zoom-out');
    const zoomLevel = document.getElementById('zoom-level');
    const fullscreenBtn = document.getElementById('fullscreen');
    const pdfWrapper = document.querySelector('.pdf-viewer-wrapper');
    
    let currentZoom = 100;

    // Zoom In Function
    zoomInBtn.addEventListener('click', function() {
        if (currentZoom < 200) {
            currentZoom += 10;
            updateZoom();
        }
    });

    // Zoom Out Function
    zoomOutBtn.addEventListener('click', function() {
        if (currentZoom > 50) {
            currentZoom -= 10;
            updateZoom();
        }
    });

    // Update Zoom Level
    function updateZoom() {
        zoomLevel.textContent = currentZoom + '%';
        
        // For iframe zoom, we need to adjust the iframe dimensions
        const iframe = pdfFrame;
        const scale = currentZoom / 100;
        
        // Adjust iframe container scaling
        iframe.style.transform = `scale(${scale})`;
        iframe.style.transformOrigin = '0 0';
        iframe.style.width = `${100 / scale}%`;
        iframe.style.height = `${600 / scale}px`;
    }

    // Fullscreen Function
    fullscreenBtn.addEventListener('click', function() {
        if (!document.fullscreenElement) {
            if (pdfWrapper.requestFullscreen) {
                pdfWrapper.requestFullscreen();
            } else if (pdfWrapper.webkitRequestFullscreen) {
                pdfWrapper.webkitRequestFullscreen();
            } else if (pdfWrapper.msRequestFullscreen) {
                pdfWrapper.msRequestFullscreen();
            }
            pdfWrapper.classList.add('fullscreen');
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            pdfWrapper.classList.remove('fullscreen');
        }
    });

    // Exit fullscreen when ESC is pressed
    document.addEventListener('fullscreenchange', exitHandler);
    document.addEventListener('webkitfullscreenchange', exitHandler);
    document.addEventListener('mozfullscreenchange', exitHandler);
    document.addEventListener('MSFullscreenChange', exitHandler);

    function exitHandler() {
        if (!document.fullscreenElement && 
            !document.webkitFullscreenElement && 
            !document.mozFullScreenElement && 
            !document.msFullscreenElement) {
            pdfWrapper.classList.remove('fullscreen');
        }
    }

    // PDF Loading State
    pdfFrame.addEventListener('load', function() {
        console.log('PDF loaded successfully');
    });

    pdfFrame.addEventListener('error', function() {
        console.error('Error loading PDF');
        const errorMessage = `
            <div class="pdf-loading">
                <p>Gagal memuat PDF. <a href="img/struktur.pdf" download>Klik di sini untuk mengunduh</a></p>
            </div>
        `;
        pdfFrame.parentNode.innerHTML = errorMessage;
    });

    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    }
});