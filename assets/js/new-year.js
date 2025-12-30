// ============================================
// EFECTOS DE AÑO NUEVO 2025
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Activar modo Año Nuevo
    document.body.classList.add('new-year-mode');
    
    // Inicializar todos los efectos
    initNewYearEffects();
});

function initNewYearEffects() {
    // Crear contenedores
    createContainers();
    
    // Iniciar efectos
    startFireworks();
    startConfetti();
    startGoldenSparkles();
    startShootingStars();
    startFloating2025();
    showNewYearBanner();
    startCountdown();
    
    // Agregar brillo a los botones
    addButtonGlow();
}

// Crear contenedores necesarios
function createContainers() {
    // Contenedor de fuegos artificiales
    const fireworksContainer = document.createElement('div');
    fireworksContainer.className = 'fireworks-container';
    fireworksContainer.id = 'fireworks-container';
    document.body.appendChild(fireworksContainer);
    
    // Contenedor de confeti
    const confettiContainer = document.createElement('div');
    confettiContainer.className = 'confetti-container';
    confettiContainer.id = 'confetti-container';
    document.body.appendChild(confettiContainer);
}

// Fuegos artificiales
function startFireworks() {
    setInterval(() => {
        createFirework();
    }, 2000);
}

function createFirework() {
    const container = document.getElementById('fireworks-container');
    if (!container) return;
    
    const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ffd700', '#ff1493'];
    const x = Math.random() * window.innerWidth;
    const y = Math.random() * (window.innerHeight * 0.6);
    
    // Crear múltiples partículas para el efecto de explosión
    for (let i = 0; i < 30; i++) {
        const particle = document.createElement('div');
        particle.className = 'firework';
        particle.style.left = x + 'px';
        particle.style.top = y + 'px';
        particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        
        // Calcular dirección aleatoria
        const angle = (Math.PI * 2 * i) / 30;
        const velocity = 50 + Math.random() * 100;
        const tx = Math.cos(angle) * velocity;
        const ty = Math.sin(angle) * velocity;
        
        particle.style.setProperty('--tx', tx + 'px');
        particle.style.setProperty('--ty', ty + 'px');
        
        container.appendChild(particle);
        
        // Eliminar después de la animación
        setTimeout(() => {
            particle.remove();
        }, 1500);
    }
}

// Confeti
function startConfetti() {
    setInterval(() => {
        createConfetti();
    }, 300);
}

function createConfetti() {
    const container = document.getElementById('confetti-container');
    if (!container) return;
    
    const colors = ['#ffd700', '#ff6347', '#4169e1', '#32cd32', '#ff1493', '#00ced1'];
    const shapes = ['square', 'circle', 'triangle'];
    
    const confetti = document.createElement('div');
    confetti.className = 'confetti';
    confetti.style.left = Math.random() * 100 + '%';
    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
    confetti.style.animationDuration = (3 + Math.random() * 3) + 's';
    confetti.style.animationDelay = Math.random() + 's';
    
    // Forma aleatoria
    const shape = shapes[Math.floor(Math.random() * shapes.length)];
    if (shape === 'circle') {
        confetti.style.borderRadius = '50%';
    } else if (shape === 'triangle') {
        confetti.style.width = '0';
        confetti.style.height = '0';
        confetti.style.borderLeft = '5px solid transparent';
        confetti.style.borderRight = '5px solid transparent';
        confetti.style.borderBottom = '10px solid ' + confetti.style.backgroundColor;
        confetti.style.backgroundColor = 'transparent';
    }
    
    container.appendChild(confetti);
    
    // Eliminar después de la animación
    setTimeout(() => {
        confetti.remove();
    }, 6000);
}

// Brillos dorados
function startGoldenSparkles() {
    setInterval(() => {
        createGoldenSparkle();
    }, 500);
}

function createGoldenSparkle() {
    const sparkle = document.createElement('div');
    sparkle.className = 'golden-sparkle';
    sparkle.style.left = Math.random() * 100 + '%';
    sparkle.style.top = Math.random() * 100 + '%';
    sparkle.style.animationDelay = Math.random() * 2 + 's';
    
    document.body.appendChild(sparkle);
    
    setTimeout(() => {
        sparkle.remove();
    }, 2000);
}

// Estrellas fugaces
function startShootingStars() {
    setInterval(() => {
        createShootingStar();
    }, 5000);
}

function createShootingStar() {
    const star = document.createElement('div');
    star.className = 'shooting-star';
    star.style.left = Math.random() * (window.innerWidth - 300) + 'px';
    star.style.top = Math.random() * (window.innerHeight / 2) + 'px';
    
    document.body.appendChild(star);
    
    setTimeout(() => {
        star.remove();
    }, 1500);
}

// Números 2025 flotando
function startFloating2025() {
    setInterval(() => {
        createFloating2025();
    }, 8000);
}

function createFloating2025() {
    const number = document.createElement('div');
    number.className = 'floating-2025';
    number.textContent = '2026';
    number.style.left = Math.random() * (window.innerWidth - 200) + 'px';
    number.style.animationDuration = (15 + Math.random() * 10) + 's';
    
    document.body.appendChild(number);
    
    setTimeout(() => {
        number.remove();
    }, 25000);
}

// Banner de Año Nuevo
function showNewYearBanner() {
    const banner = document.createElement('div');
    banner.className = 'new-year-banner';
    banner.id = 'new-year-banner';
    
    // Verificar si ya es 2026 o todavía es 2025
    const now = new Date();
    const newYear2026 = new Date('January 1, 2026 00:00:00');
    
    if (now >= newYear2026) {
        // Ya es 2026 o posterior
        banner.innerHTML = '<span class="emoji">🎉</span> ¡Feliz Año Nuevo 2026! <span class="emoji">🎊</span>';
    } else {
        // Todavía es 2025
        banner.innerHTML = '<span class="emoji">⏰</span> ¡El año 2025 se nos va! <span class="emoji">🎆</span>';
    }
    
    document.body.appendChild(banner);
    
    // Actualizar el banner cada minuto por si cambia el año
    setInterval(() => {
        const currentNow = new Date();
        if (currentNow >= newYear2026) {
            banner.innerHTML = '<span class="emoji">🎉</span> ¡Feliz Año Nuevo 2026! <span class="emoji">🎊</span>';
        }
    }, 60000); // Revisar cada minuto
}

// Contador regresivo
function startCountdown() {
    const countdownContainer = document.createElement('div');
    countdownContainer.className = 'countdown-container';
    countdownContainer.innerHTML = `
        <div class="label">Cuenta Regresiva para 2026</div>
        <div class="time">
            <div class="time-unit">
                <div class="time-value" id="days">00</div>
                <div class="time-label">Días</div>
            </div>
            <div class="time-unit">
                <div class="time-value" id="hours">00</div>
                <div class="time-label">Horas</div>
            </div>
            <div class="time-unit">
                <div class="time-value" id="minutes">00</div>
                <div class="time-label">Min</div>
            </div>
            <div class="time-unit">
                <div class="time-value" id="seconds">00</div>
                <div class="time-label">Seg</div>
            </div>
        </div>
    `;
    
    document.body.appendChild(countdownContainer);
    
    // Actualizar contador cada segundo
    updateCountdown();
    setInterval(updateCountdown, 1000);
}

function updateCountdown() {
    const newYear = new Date('January 1, 2026 00:00:00').getTime();
    const now = new Date().getTime();
    const distance = newYear - now;
    
    if (distance < 0) {
        // Si ya pasó el año nuevo, mostrar mensaje
        document.querySelector('.countdown-container .label').textContent = '¡Feliz 2026!';
        document.getElementById('days').textContent = '00';
        document.getElementById('hours').textContent = '00';
        document.getElementById('minutes').textContent = '00';
        document.getElementById('seconds').textContent = '00';
        
        // Actualizar el banner si existe
        const banner = document.getElementById('new-year-banner');
        if (banner) {
            banner.innerHTML = '<span class="emoji">🎉</span> ¡Feliz Año Nuevo 2026! <span class="emoji">🎊</span>';
        }
        
        return;
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    document.getElementById('days').textContent = String(days).padStart(2, '0');
    document.getElementById('hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
    
    // Si faltan menos de 10 segundos, crear efecto especial de cuenta regresiva
    if (days === 0 && hours === 0 && minutes === 0 && seconds <= 10 && seconds > 0) {
        // Aumentar la intensidad de los fuegos artificiales
        createFirework();
        createFirework();
    }
    
    // Cuando llegue a 0, celebración masiva
    if (days === 0 && hours === 0 && minutes === 0 && seconds === 0) {
        celebrateNewYear();
    }
}

// Función especial de celebración cuando llega el Año Nuevo
function celebrateNewYear() {
    // Crear explosión masiva de fuegos artificiales
    for (let i = 0; i < 20; i++) {
        setTimeout(() => {
            createFirework();
        }, i * 100);
    }
    
    // Crear lluvia de confeti
    for (let i = 0; i < 100; i++) {
        setTimeout(() => {
            createConfetti();
        }, Math.random() * 3000);
    }
    
    // Esta función solo se ejecuta una vez
    updateCountdown = function() {
        const banner = document.getElementById('new-year-banner');
        if (banner) {
            banner.innerHTML = '<span class="emoji">🎉</span> ¡Feliz Año Nuevo 2026! <span class="emoji">🎊</span>';
        }
    };
}

// Agregar brillo a los botones
function addButtonGlow() {
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.classList.add('btn-new-year-glow');
    });
}

// Explosión especial al hacer clic (opcional)
document.addEventListener('click', function(e) {
    // Crear mini explosión de confeti en el clic
    for (let i = 0; i < 10; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = e.clientX + 'px';
        confetti.style.top = e.clientY + 'px';
        confetti.style.position = 'fixed';
        confetti.style.animationDuration = '1s';
        confetti.style.backgroundColor = ['#ffd700', '#ff6347', '#4169e1'][Math.floor(Math.random() * 3)];
        
        document.body.appendChild(confetti);
        
        setTimeout(() => {
            confetti.remove();
        }, 1000);
    }
});