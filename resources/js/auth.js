// Helper para obtener el token de autenticación
function getAuthToken() {
    // Intentar obtener de localStorage
    let token = localStorage.getItem('api_token');
    
    // Si no está en localStorage, intentar obtener de la sesión PHP
    if (!token) {
        // Leer de un meta tag que podemos agregar en el layout
        const metaToken = document.querySelector('meta[name="api-token"]');
        if (metaToken) {
            token = metaToken.getAttribute('content');
        }
    }
    
    return token || '';
}

// Helper para hacer peticiones autenticadas
async function makeAuthRequest(url, options = {}) {
    const token = getAuthToken();
    
    const defaultOptions = {
        headers: {
            'Authorization': `Bearer ${token}`,
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    };
    
    return fetch(url, { ...defaultOptions, ...options });
}