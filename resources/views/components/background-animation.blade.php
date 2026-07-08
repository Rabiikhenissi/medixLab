<div class="fixed inset-0 pointer-events-none overflow-hidden z-0 bg-[#F8FAFC]">
    <!-- Ambient Glow Blobs (Gradients) -->
    <div class="absolute top-0 left-0 w-[45vw] h-[45vw] ambient-glow-1 opacity-80"></div>
    <div class="absolute bottom-0 right-0 w-[50vw] h-[50vw] ambient-glow-2 opacity-80"></div>
    <div class="absolute top-1/3 right-1/4 w-[35vw] h-[35vw] ambient-glow-3 opacity-60"></div>

    <!-- Interactive Particle Canvas -->
    <canvas id="interactive-particles" class="absolute inset-0 w-full h-full pointer-events-none" style="opacity: 0.85; mix-blend-mode: multiply;"></canvas>

    <!-- Floating Medical Outline Icons -->
    <!-- Stethoscope -->
    <svg class="floating-icon absolute w-16 h-16 text-blue-500/10 top-[15%] left-[8%] hidden md:block" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="animation-duration: 14s; animation-delay: 0s;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v6m0 0a4 4 0 00-4 4v2a4 4 0 008 0v-2a4 4 0 00-4-4zm0 0V4m-6 8h12M6 12a6 6 0 0012 0" />
    </svg>

    <!-- Pulse Wave -->
    <svg class="floating-icon absolute w-20 h-20 text-teal-500/10 top-[25%] right-[10%] hidden md:block" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" style="animation-duration: 16s; animation-delay: 1.5s;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h1.5l2.42-6.58a1 1 0 011.914 0l3.332 9.09a1 1 0 001.916 0l2.42-6.51H22" />
    </svg>

    <!-- Thermometer -->
    <svg class="floating-icon absolute w-14 h-14 text-indigo-500/10 bottom-[18%] right-[22%] hidden md:block" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" style="animation-duration: 18s; animation-delay: 3s;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v10.5a3.5 3.5 0 106 0V3a3 3 0 00-6 0zM12 11.5v3M12 17h.01" />
    </svg>

    <!-- Heart -->
    <svg class="floating-icon absolute w-16 h-16 text-rose-500/10 bottom-[28%] left-[12%] hidden md:block" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="animation-duration: 15s; animation-delay: 2s;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>

    <!-- Eye -->
    <svg class="floating-icon absolute w-14 h-14 text-cyan-500/10 top-[55%] left-[5%] hidden md:block" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" style="animation-duration: 13s; animation-delay: 4.5s;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>

    <!-- Hospital Building -->
    <svg class="floating-icon absolute w-18 h-18 text-purple-500/10 bottom-[8%] left-[28%] hidden md:block" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="animation-duration: 20s; animation-delay: 0.5s;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" />
    </svg>

    <!-- Pill Capsule -->
    <svg class="floating-icon absolute w-12 h-12 text-teal-500/10 top-[8%] right-[25%] hidden md:block" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" style="animation-duration: 12s; animation-delay: 3.5s;">
        <rect x="5" y="5" width="14" height="14" rx="7" transform="rotate(45 12 12)" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const canvas = document.getElementById("interactive-particles");
        if (!canvas) return;
        const ctx = canvas.getContext("2d");

        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        const particles = [];
        const particleCount = Math.min(80, Math.floor((width * height) / 18000));
        const connectionDistance = 110;
        const mouseRepelRadius = 150;
        const mouseForce = 0.8;

        const mouse = {
            x: null,
            y: null,
            targetX: null,
            targetY: null
        };

        const colors = [
            "rgba(0, 102, 255, 0.22)",   // Blue
            "rgba(13, 148, 136, 0.22)",  // Teal
            "rgba(124, 58, 237, 0.18)",  // Purple
            "rgba(245, 158, 11, 0.22)"    // Amber
        ];

        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.size = Math.random() * 2.5 + 1.2;
                this.color = colors[Math.floor(Math.random() * colors.length)];
                
                // Slow ambient drift velocities
                this.vx = (Math.random() - 0.5) * 0.35;
                this.vy = (Math.random() - 0.5) * 0.35;
                
                // Track home velocity to return to
                this.baseVx = this.vx;
                this.baseVy = this.vy;

                // Current velocity with repulsion
                this.dx = 0;
                this.dy = 0;
            }

            update() {
                // Return slowly to base velocity (inertia/friction)
                this.vx += (this.baseVx - this.vx) * 0.05;
                this.vy += (this.baseVy - this.vy) * 0.05;

                // Mouse interaction
                if (mouse.x !== null) {
                    const diffX = this.x - mouse.x;
                    const diffY = this.y - mouse.y;
                    const dist = Math.hypot(diffX, diffY);

                    if (dist < mouseRepelRadius) {
                        const force = (mouseRepelRadius - dist) / mouseRepelRadius;
                        // Calculate push direction
                        const angle = Math.atan2(diffY, diffX);
                        
                        // Push away from mouse
                        this.vx += Math.cos(angle) * force * mouseForce;
                        this.vy += Math.sin(angle) * force * mouseForce;
                    }
                }

                // Apply velocity
                this.x += this.vx;
                this.y += this.vy;

                // Wrap around edges with margin
                const margin = 20;
                if (this.x < -margin) this.x = width + margin;
                if (this.x > width + margin) this.x = -margin;
                if (this.y < -margin) this.y = height + margin;
                if (this.y > height + margin) this.y = -margin;
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
            }
        }

        // Initialize particles
        for (let i = 0; i < particleCount; i++) {
            particles.push(new Particle());
        }

        // Track mouse
        window.addEventListener("mousemove", (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });

        window.addEventListener("mouseleave", () => {
            mouse.x = null;
            mouse.y = null;
        });

        // Resize handler
        window.addEventListener("resize", () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        // Animation Loop
        function animate() {
            ctx.clearRect(0, 0, width, height);

            // Draw connections first
            for (let i = 0; i < particles.length; i++) {
                const p1 = particles[i];
                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dist = Math.hypot(p1.x - p2.x, p1.y - p2.y);

                    if (dist < connectionDistance) {
                        // Calculate opacity based on distance
                        const alpha = (1 - dist / connectionDistance) * 0.08;
                        ctx.beginPath();
                        ctx.moveTo(p1.x, p1.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.strokeStyle = `rgba(100, 116, 139, ${alpha})`;
                        ctx.lineWidth = 0.8;
                        ctx.stroke();
                    }
                }
            }

            // Draw and update particles
            particles.forEach(p => {
                p.update();
                p.draw();
            });

            requestAnimationFrame(animate);
        }

        animate();
    });
</script>
