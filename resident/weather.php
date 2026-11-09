<?php
// resident/weather.php
require_once '../includes/auth.php';
require_once '../config/database.php';

require_login();

include '../includes/header.php';
?>

<div class="hero" style="padding: 2rem; margin-bottom: 2rem; text-align: left;">
    <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: white;">🌤️ Weather Forecast</h1>
    <p style="color: #cbd5e1; margin: 0;">Live data from OpenWeatherMap API.</p>
</div>

<div id="weather-container" style="background: var(--surface); padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border);">
    <p style="text-align: center; color: var(--text-muted);">Loading weather data...</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/weather.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('weather-container');
            
            // Check kung naay valid data gikan sa OpenWeatherMap
            if (!data.main || data.main.temp === 'N/A') {
                container.innerHTML = '<p style="color: red; text-align: center;">Weather data temporarily unavailable.</p>';
                return;
            }

            const temp = data.main.temp;
            const humidity = data.main.humidity;
            const wind = data.wind.speed;
            const desc = data.weather[0].description;
            const iconCode = data.weather[0].icon;
            
            // I-load ang icon gikan sa OpenWeatherMap
            const iconUrl = `https://openweathermap.org/img/wn/${iconCode}@4x.png`;

            container.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h2 style="color: var(--primary); margin-bottom: 0.25rem;">Current Weather (Manila)</h2>
                        <p style="color: var(--text-muted); text-transform: capitalize; font-size: 1.1rem;">${desc}</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <img src="${iconUrl}" alt="${desc}" style="width: 100px; height: 100px;">
                        <div style="font-size: 3.5rem; font-weight: 800; color: var(--accent);">${Math.round(temp)}°C</div>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.9rem; color: var(--text-muted);">Wind Speed</div>
                        <div style="font-size: 1.2rem; font-weight: 600;">${wind} m/s</div>
                    </div>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                        <div style="font-size: 0.9rem; color: var(--text-muted);">Humidity</div>
                        <div style="font-size: 1.2rem; font-weight: 600;">${humidity}%</div>
                    </div>
                </div>
                
                <p style="text-align: center; font-size: 0.8rem; color: #9ca3af; margin-top: 2rem;">
                    Weather data by <a href="https://openweathermap.org/" target="_blank" style="color: #9ca3af; text-decoration: underline;">OpenWeatherMap</a>
                </p>
            `;
        })
        .catch(err => {
            document.getElementById('weather-container').innerHTML = '<p style="color: red; text-align: center;">Failed to load weather data. Please check your internet connection.</p>';
        });
});
</script>

<?php include '../includes/footer.php'; ?>