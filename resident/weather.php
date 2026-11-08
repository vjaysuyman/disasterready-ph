<?php
// resident/weather.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_login();

include '../includes/header.php';
?>

<div class="hero" style="padding: 2rem; margin-bottom: 2rem; text-align: left;">
    <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: white;">🌤️ Weather Forecast</h1>
    <p style="color: #cbd5e1; margin: 0;">Live data from Open-Meteo API.</p>
</div>

<div id="weather-container" style="background: var(--surface); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border);">
    <p style="text-align: center; color: var(--text-muted);">Loading weather data...</p>
</div>

<script>
// Weather Codes gikan sa Open-Meteo (para mahimong text ug emoji)
function getWeatherInfo(code) {
    const codes = {
        0: { desc: 'Clear sky', icon: '☀️' },
        1: { desc: 'Mainly clear', icon: '🌤️' },
        2: { desc: 'Partly cloudy', icon: '⛅' },
        3: { desc: 'Overcast', icon: '☁️' },
        45: { desc: 'Fog', icon: '🌫️' },
        48: { desc: 'Depositing rime fog', icon: '🌫️' },
        51: { desc: 'Light drizzle', icon: '🌦️' },
        53: { desc: 'Moderate drizzle', icon: '🌦️' },
        55: { desc: 'Dense drizzle', icon: '🌧️' },
        61: { desc: 'Slight rain', icon: '🌧️' },
        63: { desc: 'Moderate rain', icon: '🌧️' },
        65: { desc: 'Heavy rain', icon: '🌧️' },
        71: { desc: 'Slight snow', icon: '🌨️' },
        73: { desc: 'Moderate snow', icon: '🌨️' },
        75: { desc: 'Heavy snow', icon: '🌨️' },
        80: { desc: 'Slight rain showers', icon: '🌦️' },
        81: { desc: 'Moderate rain showers', icon: '🌧️' },
        82: { desc: 'Violent rain showers', icon: '⛈️' },
        95: { desc: 'Thunderstorm', icon: '⛈️' },
        96: { desc: 'Thunderstorm with slight hail', icon: '⛈️' },
        99: { desc: 'Thunderstorm with heavy hail', icon: '⛈️' }
    };
    return codes[code] || { desc: 'Unknown', icon: '❓' };
}

// Kani nga endpoint kay mo-fetch sa weather.php API proxy
fetch('/api/weather.php')
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('weather-container');
        
        if (!data.current || !data.daily) {
            container.innerHTML = '<p style="color: red; text-align: center;">Failed to load weather data.</p>';
            return;
        }

        // 1. Current Weather
        const current = data.current;
        const currentInfo = getWeatherInfo(current.weather_code);
        
        let html = `
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="color: var(--primary); margin-bottom: 0.25rem;">Current Weather (Manila)</h2>
                    <p style="color: var(--text-muted);">${currentInfo.desc}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <div style="font-size: 4rem;">${currentInfo.icon}</div>
                    <div style="font-size: 3.5rem; font-weight: 800; color: var(--accent);">${current.temperature_2m}°C</div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2.5rem;">
                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Wind Speed</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">${current.wind_speed_10m} km/h</div>
                </div>
                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Humidity</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">${current.relative_humidity_2m}%</div>
                </div>
            </div>

            <h3 style="color: var(--primary); margin-bottom: 1rem;">7-Day Forecast</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem;">
        `;

        // 2. 7-Day Forecast
        for (let i = 0; i < data.daily.time.length; i++) {
            const date = new Date(data.daily.time[i]);
            const dayName = date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
            const info = getWeatherInfo(data.daily.weather_code[i]);
            const maxTemp = data.daily.temperature_2m_max[i];
            const minTemp = data.daily.temperature_2m_min[i];

            html += `
                <div style="background: white; border: 1px solid var(--border); border-radius: 8px; padding: 1rem; text-align: center;">
                    <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; color: var(--primary);">${dayName}</div>
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">${info.icon}</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; height: 40px; display: flex; align-items: center; justify-content: center;">${info.desc}</div>
                    <div style="font-weight: 600; color: var(--accent);">${maxTemp}° <span style="color: var(--text-muted); font-weight: 400;">/ ${minTemp}°</span></div>
                </div>
            `;
        }

        html += `</div>
            <p style="text-align: center; font-size: 0.8rem; color: #9ca3af; margin-top: 2rem;">
                Weather data by <a href="https://open-meteo.com/" target="_blank" style="color: #9ca3af; text-decoration: underline;">Open-Meteo.com</a>
            </p>
        `;

        container.innerHTML = html;
    })
    .catch(err => {
        document.getElementById('weather-container').innerHTML = '<p style="color: red; text-align: center;">Failed to load weather data. Please check your internet connection.</p>';
    });
</script>

<?php include '../includes/footer.php'; ?>