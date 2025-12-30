<?php
include("cabecera.php");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600&display=swap');
    
    .ranking-container {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        min-height: 100vh;
        padding: 40px 0;
        position: relative;
        overflow: hidden;
    }
    
    .ranking-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255,255,255,0.08) 0%, transparent 50%);
        pointer-events: none;
    }
    
    .page-header {
        text-align: center;
        color: white;
        margin-bottom: 50px;
        position: relative;
        z-index: 1;
        animation: fadeInDown 0.8s ease-out;
    }
    
    .page-header h1 {
        font-family: 'Orbitron', sans-serif;
        font-size: 3.5rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 10px;
        text-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    
    .page-header .subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 1.2rem;
        font-weight: 300;
        opacity: 0.95;
        letter-spacing: 1px;
    }
    
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
        animation: fadeInUp 0.8s ease-out 0.2s both;
    }
    
    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 50px rgba(0,0,0,0.25);
        border-color: #3b82f6;
    }
    
    .stat-card .icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-card .value {
        font-family: 'Orbitron', sans-serif;
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin: 10px 0;
    }
    
    .stat-card .label {
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .ranking-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 25px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        position: relative;
        z-index: 1;
        animation: fadeInUp 0.8s ease-out 0.4s both;
    }
    
    .ranking-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 15px;
    }
    
    .ranking-table thead th {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
        color: #4a5568;
        padding: 15px 20px;
        border-bottom: 3px solid #e2e8f0;
        background: transparent;
    }
    
    .ranking-table tbody tr {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        animation: slideInLeft 0.5s ease-out both;
    }
    
    .ranking-table tbody tr:nth-child(1) { animation-delay: 0.1s; }
    .ranking-table tbody tr:nth-child(2) { animation-delay: 0.15s; }
    .ranking-table tbody tr:nth-child(3) { animation-delay: 0.2s; }
    .ranking-table tbody tr:nth-child(4) { animation-delay: 0.25s; }
    .ranking-table tbody tr:nth-child(5) { animation-delay: 0.3s; }
    
    .ranking-table tbody tr:hover {
        transform: translateX(10px) scale(1.02);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }
    
    .ranking-table tbody td {
        padding: 25px 20px;
        font-family: 'Inter', sans-serif;
        color: #2d3748;
        border: none;
    }
    
    .ranking-table tbody tr td:first-child {
        border-top-left-radius: 15px;
        border-bottom-left-radius: 15px;
    }
    
    .ranking-table tbody tr td:last-child {
        border-top-right-radius: 15px;
        border-bottom-right-radius: 15px;
    }
    
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        font-size: 1.2rem;
        position: relative;
    }
    
    .rank-badge.top-1 {
        background: linear-gradient(135deg, #FFD700, #FFA500);
        color: white;
        box-shadow: 0 5px 20px rgba(255, 215, 0, 0.4);
        animation: pulse 2s ease-in-out infinite;
    }
    
    .rank-badge.top-2 {
        background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
        color: white;
        box-shadow: 0 5px 20px rgba(192, 192, 192, 0.4);
    }
    
    .rank-badge.top-3 {
        background: linear-gradient(135deg, #CD7F32, #8B4513);
        color: white;
        box-shadow: 0 5px 20px rgba(205, 127, 50, 0.4);
    }
    
    .rank-badge.regular {
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        color: white;
    }
    
    .cliente-name {
        font-weight: 600;
        font-size: 1.1rem;
        color: #2d3748;
    }
    
    .total-compras {
        font-family: 'Orbitron', sans-serif;
        font-weight: 700;
        font-size: 1.3rem;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .search-container {
        margin-bottom: 30px;
        position: relative;
    }
    
    .search-input {
        width: 100%;
        padding: 15px 50px 15px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    
    .search-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 1.2rem;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .trophy-icon {
        font-size: 2rem;
        margin-right: 10px;
        vertical-align: middle;
    }
    
    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: #718096;
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2rem;
        }
        
        .ranking-card {
            padding: 20px;
        }
        
        .ranking-table tbody tr:hover {
            transform: scale(1.02);
        }
    }
</style>

<div class="ranking-container">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-trophy trophy-icon"></i>RANKING DE CLIENTES</h1>
            <div class="subtitle">Los mejores clientes de nuestra empresa</div>
        </div>
        
        <div class="stats-overview">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <div class="value" id="totalClientes">0</div>
                <div class="label">Total Clientes</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="value" id="totalVentas">S/ 0.00</div>
                <div class="label">Ventas Totales</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-chart-line"></i></div>
                <div class="value" id="promedioCompra">S/ 0.00</div>
                <div class="label">Promedio por Cliente</div>
            </div>
        </div>
        
        <div class="ranking-card">
            <div class="search-container">
                <input type="text" id="searchInput" class="search-input" placeholder="Buscar cliente por nombre...">
                <i class="fas fa-search search-icon"></i>
            </div>
            
            <div class="table-responsive">
                <table class="ranking-table" id="rankingTable">
                    <thead>
                        <tr>
                            <th>RANKING</th>
                            <th>CLIENTE</th>
                            <th>TOTAL COMPRAS</th>
                            <th>% DEL TOTAL</th>
                        </tr>
                    </thead>
                    <tbody id="rankingTableBody">
                        <!-- Los datos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    cargarRankingClientes();
    
    // Búsqueda en tiempo real
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#rankingTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

function cargarRankingClientes() {
    $.ajax({
        url: 'logica/clssConsultas.php',
        type: 'POST',
        data: {
            accion: 'RANKING_CLIENTES'
        },
        dataType: 'json',
        success: function(datos) {
            if (datos && datos.length > 0) {
                mostrarRanking(datos);
                calcularEstadisticas(datos);
            } else {
                $('#rankingTableBody').html('<tr><td colspan="4" class="no-data"><i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 20px;"></i>No hay datos de clientes disponibles</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar el ranking:", error);
            $('#rankingTableBody').html('<tr><td colspan="4" class="no-data"><i class="fas fa-exclamation-triangle" style="font-size: 3rem; display: block; margin-bottom: 20px; color: #f56565;"></i>Error al cargar los datos. Por favor, intenta de nuevo.</td></tr>');
        }
    });
}

function mostrarRanking(datos) {
    var tbody = '';
    var totalVentas = datos.reduce((sum, item) => sum + parseFloat(item.total_compras_acumulado), 0);
    
    datos.forEach(function(cliente, index) {
        var ranking = index + 1;
        var badgeClass = 'regular';
        
        if (ranking === 1) badgeClass = 'top-1';
        else if (ranking === 2) badgeClass = 'top-2';
        else if (ranking === 3) badgeClass = 'top-3';
        
        var porcentaje = ((parseFloat(cliente.total_compras_acumulado) / totalVentas) * 100).toFixed(2);
        
        tbody += '<tr>' +
            '<td><div class="rank-badge ' + badgeClass + '">' + ranking + '</div></td>' +
            '<td><div class="cliente-name">' + cliente.nombre_cliente + '</div></td>' +
            '<td><div class="total-compras">S/ ' + parseFloat(cliente.total_compras_acumulado).toFixed(2) + '</div></td>' +
            '<td><strong>' + porcentaje + '%</strong></td>' +
        '</tr>';
    });
    
    $('#rankingTableBody').html(tbody);
}

function calcularEstadisticas(datos) {
    var totalClientes = datos.length;
    var totalVentas = datos.reduce((sum, item) => sum + parseFloat(item.total_compras_acumulado), 0);
    var promedioCompra = totalVentas / totalClientes;
    
    // Animación de contadores
    animateValue('totalClientes', 0, totalClientes, 1000);
    animateValue('totalVentas', 0, totalVentas, 1500, true);
    animateValue('promedioCompra', 0, promedioCompra, 1500, true);
}

function animateValue(id, start, end, duration, isCurrency = false) {
    var obj = document.getElementById(id);
    var range = end - start;
    var minTimer = 50;
    var stepTime = Math.abs(Math.floor(duration / range));
    stepTime = Math.max(stepTime, minTimer);
    
    var startTime = null;
    
    function animate(currentTime) {
        if (!startTime) startTime = currentTime;
        var progress = currentTime - startTime;
        var percentage = Math.min(progress / duration, 1);
        var current = start + (range * percentage);
        
        if (isCurrency) {
            obj.textContent = 'S/ ' + current.toFixed(2);
        } else {
            obj.textContent = Math.round(current);
        }
        
        if (percentage < 1) {
            requestAnimationFrame(animate);
        } else {
            // Asegurar que muestre el valor final exacto
            if (isCurrency) {
                obj.textContent = 'S/ ' + end.toFixed(2);
            } else {
                obj.textContent = Math.round(end);
            }
        }
    }
    
    requestAnimationFrame(animate);
}
</script>

<?php include("pie.php"); ?>