<?php
// resident/weather.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_login();

include '../includes/header.php';
?>

<div class="hero" style="padding: 2rem; margin-bottom: 2rem; text-align: left;">
    <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: white;">🌤️ Weather Forecast</h1>
    <p style="color: #cbd5e1; margin: 0;">Live data from Visual Crossing Weather API.</p>
</div>

<div id="weather-container" style="background: var(--surface); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border);">
    <p style="text-align: center; color: var(--text-muted);">Loading weather data...</p>
</div>

<script>
// Visual Crossing icon codes -> emoji
function getWeatherInfo(icon) {
    const icons = {
        'clear-day': { desc: 'Clear sky', icon: '☀️' },
        'clear-night': { desc: 'Clear night', icon: '🌙' },
        'partly-cloudy-day': { desc: 'Partly cloudy', icon: '⛅' },
        'partly-cloudy-night': { desc: 'Partly cloudy', icon: '☁️' },
        'cloudy': { desc: 'Cloudy', icon: '☁️' },
        'fog': { desc: 'Fog', icon: '🌫️' },
        'wind': { desc: 'Windy', icon: '💨' },
        'rain': { desc: 'Rain', icon: '🌧️' },
        'showers-day': { desc: 'Showers', icon: '🌦️' },
        'showers-night': { desc: 'Showers', icon: '🌦️' },
        'thunder-rain': { desc: 'Thunderstorm', icon: '⛈️' },
        'thunder-showers-day': { desc: 'Thunder showers', icon: '⛈️' },
        'thunder-showers-night': { desc: 'Thunder showers', icon: '⛈️' },
        'snow': { desc: 'Snow', icon: '🌨️' },
        'snow-showers-day': { desc: 'Snow showers', icon: '🌨️' },
        'snow-showers-night': { desc: 'Snow showers', icon: '🌨️' },
        'sleet': { desc: 'Sleet', icon: '🌨️' },
        'hail': { desc: 'Hail', icon: '🧊' }
    };
    return icons[icon] || { desc: 'Unknown', icon: '❓' };
}

fetch('/api/weather.php')
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('weather-container');

        // Visual Crossing structure: currentConditions + days[]
        if (!data.currentConditions || !data.days) {
            container.innerHTML = '<p style="color: red; text-align: center;">Failed to load weather data.</p>';
            return;
        }

        // 1. Current Weather
        const current = data.currentConditions;
        const currentInfo = getWeatherInfo(current.icon);

        let html = `
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="color: var(--primary); margin-bottom: 0.25rem;">Current Weather (Manila)</h2>
                    <p style="color: var(--text-muted);">${current.conditions || currentInfo.desc}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <div style="font-size: 4rem;">${currentInfo.icon}</div>
                    <div style="font-size: 3.5rem; font-weight: 800; color: var(--accent);">${current.temp}°C</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2.5rem;">
                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Wind Speed</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">${current.windspeed} km/h</div>
                </div>
                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 0.9rem; color: var(--text-muted);">Humidity</div>
                    <div style="font-size: 1.2rem; font-weight: 600;">${current.humidity}%</div>
                </div>
            </div>

            <h3 style="color: var(--primary); margin-bottom: 1rem;">7-Day Forecast</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem;">
        `;

        // 2. 7-Day Forecast (Visual Crossing: data.days[])
        const days = data.days.slice(0, 7);
        for (let i = 0; i < days.length; i++) {
            const day = days[i];
            const date = new Date(day.datetime);
            const dayName = date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
            const info = getWeatherInfo(day.icon);

            html += `
                <div style="background: white; border: 1px solid var(--border); border-radius: 8px; padding: 1rem; text-align: center;">
                    <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; color: var(--primary);">${dayName}</div>
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">${info.icon}</div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem; height: 40px; display: flex; align-items: center; justify-content: center;">${day.conditions || info.desc}</div>
                    <div style="font-weight: 600; color: var(--accent);">${day.tempmax}° <span style="color: var(--text-muted); font-weight: 400;">/ ${day.tempmin}°</span></div>
                </div>
            `;
        }

        html += `</div>
            <p style="text-align: center; font-size: 0.8rem; color: #9ca3af; margin-top: 2rem;">
                Weather data by <a href="https://www.visualcrossing.com/" target="_blank" style="color: #9ca3af; text-decoration: underline;">Visual Crossing Weather</a>
            </p>
        `;

        container.innerHTML = html;
    })
    .catch(err => {
        document.getElementById('weather-container').innerHTML = '<p style="color: red; text-align: center;">Failed to load weather data. Please check your internet connection.</p>';
    });
</script>

<?php include '../includes/footer.php'; ?>