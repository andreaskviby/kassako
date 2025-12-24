<svg viewBox="0 0 200 48" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Logo Mark -->
    <defs>
        <linearGradient id="logoGradientFull" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#1A3D2E"/>
            <stop offset="100%" style="stop-color:#2D5A45"/>
        </linearGradient>
    </defs>

    <!-- Main circle background -->
    <circle cx="24" cy="24" r="24" fill="url(#logoGradientFull)"/>

    <!-- Stylized C shape -->
    <path d="M28 12C20.268 12 14 18.268 14 26C14 33.732 20.268 40 28 40"
          stroke="#C4A962"
          stroke-width="4"
          stroke-linecap="round"
          fill="none"/>

    <!-- Chart bars inside the C -->
    <rect x="20" y="28" width="4" height="8" rx="1" fill="white"/>
    <rect x="26" y="24" width="4" height="12" rx="1" fill="white"/>
    <rect x="32" y="20" width="4" height="16" rx="1" fill="#C4A962"/>

    <!-- Upward trend arrow -->
    <path d="M32 16L36 12M36 12L36 16M36 12L32 12"
          stroke="#C4A962"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"/>

    <!-- CashDash Text -->
    <text x="56" y="32" font-family="system-ui, -apple-system, sans-serif" font-size="22" font-weight="600" fill="#1A3D2E">
        Cash<tspan fill="#C4A962">Dash</tspan>
    </text>
</svg>
