<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago - Venta #{{ $venta['id'] }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt; /* Reducido ligeramente */
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .comprobante {
            max-width: 800px;
            margin: 0 auto;
            padding: 15px; /* Reducido de 20px a 15px */
            border: 1px solid #e0e0e0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        /* Encabezado con logo centrado arriba - más compacto */
        .header {
            text-align: center;
            margin-bottom: 8px; /* Reducido de 15px */
            padding-bottom: 8px; /* Reducido de 15px */
            border-bottom: 2px solid #2c3e50; /* Borde más delgado */
        }
        .logo-container {
            margin-bottom: 5px; /* Reducido */
        }
        .logo {
            max-width: 120px; /* Reducido de 120px */
            max-height: 120px; /* Reducido de 120px */
            object-fit: contain;
        }
        
        /* Título del comprobante - más compacto */
        .titulo-container {
            text-align: center;
            margin: 3px 0 10px 0; /* Reducido */
        }
        .titulo-container h2 {
            font-size: 14pt; /* Reducido de 18pt */
            color: #34495e;
            margin: 0;
            padding: 5px 20px; /* Reducido padding */
            background: #ecf0f1;
            display: inline-block;
            border-radius: 4px;
            letter-spacing: 0.5px; /* Reducido */
            font-weight: bold;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Fecha y número de comprobante - mismo tamaño que cliente */
        .info-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px; /* Reducido */
            padding: 8px; /* Reducido */
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 9pt; /* Mismo tamaño que cliente-info */
        }
        .info-box {
            padding: 3px 8px; /* Reducido */
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 8pt; /* Reducido */
            text-transform: uppercase;
        }
        .info-value {
            font-size: 10pt; /* Reducido de 12pt */
            font-weight: bold;
            color: #2980b9;
        }
        
        /* Tabla de items - con bordes visibles */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
            border: 1px solid #ddd; /* Borde exterior de la tabla */
        }
        .items-table th {
            background: #34495e;
            color: white;
            font-weight: bold;
            padding: 8px 6px;
            text-align: center; /* Centrado */
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #2c3e50; /* Bordes entre columnas */
        }
        .items-table td {
            padding: 6px;
            border: 1px solid #ddd; /* Bordes entre celdas */
            vertical-align: top;
        }
        .items-table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .items-table tfoot tr {
            background: #f8f9fa;
            font-weight: bold;
        }
        .items-table tfoot td {
            padding: 8px 6px; /* Reducido */
            border-top: 2px solid #34495e;
        }
        
        /* Alineaciones */
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        
        /* Estilos para montos */
        .monto {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            font-size: 9pt;
        }
        .descuento {
            color: #e74c3c;
        }
        .total {
            font-size: 11pt; /* Reducido */
            color: #27ae60;
        }
        
        /* Descripción del item */
        .item-descripcion {
            font-weight: 500;
            color: #2c3e50;
            font-size: 9pt;
        }
        .item-referencia {
            font-size: 7.5pt; /* Reducido */
            color: #7f8c8d;
            margin-top: 2px;
        }
        
        /* Nota / Observaciones - más compacta */
        .nota-container {
            margin: 15px 0 10px 0; /* Reducido */
            padding: 10px; /* Reducido */
            background: #fef9e7;
            border-left: 4px solid #f39c12;
            border-radius: 0 4px 4px 0;
        }
        .nota-titulo {
            font-weight: bold;
            color: #d35400;
            margin-bottom: 3px;
            font-size: 9pt; /* Reducido */
            text-transform: uppercase;
        }
        .nota-contenido {
            color: #555;
            font-size: 9pt; /* Reducido */
            line-height: 1.4;
            margin: 0;
            font-style: italic;
        }
        
        /* Resumen de totales - más compacto */
        .resumen-totales {
            width: 280px; /* Reducido */
            margin-left: auto;
            background: #ecf0f1;
            padding: 10px; /* Reducido */
            border-radius: 4px;
            font-size: 9pt;
        }
        .resumen-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0; /* Reducido */
            border-bottom: 1px dashed #bdc3c7;
        }
        .resumen-row.total-final {
            border-bottom: none;
            border-top: 2px solid #34495e;
            margin-top: 4px; /* Reducido */
            padding-top: 8px; /* Reducido */
            font-weight: bold;
            font-size: 11pt; /* Reducido */
        }
        .resumen-label {
            color: #34495e;
        }
        .resumen-value {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        /* Pie de página - más compacto */
        .footer {
            margin-top: 20px; /* Reducido */
            padding-top: 12px; /* Reducido */
            border-top: 2px solid #34495e;
            text-align: center;
            font-size: 8.5pt; /* Reducido */
        }
        .footer-nombre {
            font-size: 12pt; /* Reducido */
            font-weight: bold;
            color: #2c3e50;
            margin: 0 0 3px 0;
        }
        .footer-direccion {
            color: #7f8c8d;
            font-size: 8.5pt; /* Reducido */
            margin: 2px 0;
        }
        .footer-whatsapp {
            color: #7f8c8d;
            font-size: 9pt; /* Reducido */
            margin: 3px 0;
            font-weight: normal;
        }
        .footer-lema {
            color: #95a5a6;
            font-size: 8pt; /* Reducido */
            margin-top: 3px;
            font-style: italic;
        }
        
        /* Badge para tipo de pago */
        .badge {
            display: inline-block;
            padding: 2px 6px; /* Reducido */
            border-radius: 3px;
            font-size: 7.5pt; /* Reducido */
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-efectivo {
            background: #27ae60;
            color: white;
        }
        .badge-tarjeta {
            background: #2980b9;
            color: white;
        }
        .badge-transferencia {
            background: #8e44ad;
            color: white;
        }
        .badge-credito {
            background: #f39c12;
            color: white;
        }
        .badge-mixto {
            background: #16a085;
            color: white;
        }
        
        /* Información del cliente - mismo tamaño que info-header */
        .cliente-info {
            margin: 10px 0; /* Reducido */
            padding: 8px; /* Reducido */
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 9pt;
        }
        .cliente-titulo {
            font-weight: bold;
            color: #34495e;
            margin-bottom: 3px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="comprobante">
        <!-- Encabezado con logo centrado arriba - más compacto -->
        <div class="header">
            <div class="logo-container">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo Logickem" class="logo">
            </div>
        </div>

        <!-- Título más pequeño y compacto -->
        <div class="titulo-container">
            <h2>COMPROBANTE DE PAGO</h2>
        </div>

        <!-- Información del comprobante - mismo tamaño que cliente -->
        <div class="info-header">
            <div class="info-box">
                <div class="info-label">No. Comprobante</div>
                <div class="info-value">{{ str_pad($venta['id'] ?? 0, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Fecha</div> <!-- Solo "Fecha" sin "y Hora" -->
                <div class="info-value">{{ $venta['fecha_solo'] ?? now()->timezone('America/Guatemala')->format('d/m/Y') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Método de Pago</div>
                <div class="info-value">
                    @php
                        $metodo = $venta['metodo_pago'] ?? 'efectivo';
                        $metodoClass = [
                            'efectivo' => 'badge-efectivo',
                            'tarjeta' => 'badge-tarjeta',
                            'transferencia' => 'badge-transferencia',
                            'credito' => 'badge-credito',
                            'mixto' => 'badge-mixto'
                        ][$metodo] ?? 'badge-efectivo';
                        
                        $metodoTexto = [
                            'efectivo' => 'Efectivo',
                            'tarjeta' => 'Tarjeta',
                            'transferencia' => 'Transferencia',
                            'credito' => 'Crédito',
                            'mixto' => 'Mixto'
                        ][$metodo] ?? $metodo;
                    @endphp
                    <span class="badge {{ $metodoClass }}">
                        {{ $metodoTexto }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Información del cliente (si existe) -->
        @if(isset($venta['cliente']) && $venta['cliente'])
        <div class="cliente-info">
            <div class="cliente-titulo">CLIENTE</div>
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <span><strong>Nombre:</strong> {{ $venta['cliente']['nombre'] ?? 'N/A' }}</span>
                @if(!empty($venta['cliente']['nit']))
                <span><strong>NIT:</strong> {{ $venta['cliente']['nit'] }}</span>
                @endif
                @if(!empty($venta['cliente']['telefono']))
                <span><strong>Tel:</strong> {{ $venta['cliente']['telefono'] }}</span>
                @endif
            </div>
        </div>
        @endif

        <!-- Tabla de items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="40%">Descripción</th>
                    <th width="10%" class="text-center">Cantidad</th>
                    <th width="15%" class="text-right">P. Unitario</th>
                    <th width="15%" class="text-right">Descuento</th> <!-- Abreviado -->
                    <th width="15%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($venta['items'] ?? [] as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="item-descripcion">{{ $item['descripcion'] }}</div>
                        @if(!empty($item['referencia']))
                        <div class="item-referencia">Ref: {{ $item['referencia'] }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item['cantidad'] }}</td>
                    <td class="text-right monto">{{ $item['precio_unitario_formateado'] ?? 'Q '.number_format($item['precio_unitario'], 2) }}</td>
                    <td class="text-right monto {{ ($item['descuento'] ?? 0) > 0 ? 'descuento' : '' }}">
                        @if(($item['descuento'] ?? 0) > 0)
                            @if(isset($item['descuento_formateado']))
                                {{ $item['descuento_formateado'] }}
                            @else
                                Q {{ number_format($item['descuento'], 2) }}
                            @endif
                        @else
                            Q 0.00
                        @endif
                    </td>
                    <td class="text-right monto">
                        {{ $item['subtotal_formateado'] ?? 'Q '.number_format($item['subtotal'] ?? ($item['cantidad'] * $item['precio_unitario']), 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">
                        No hay items en esta venta
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Nota / Observaciones (desde la base de datos) -->
        @if(!empty($venta['nota_descuentos']))
        <div class="nota-container">
            <div class="nota-titulo">NOTA</div>
            <div class="nota-contenido">
                {{ $venta['nota_descuentos'] }}
            </div>
        </div>
        @endif

        <!-- Resumen de totales -->
        <div class="resumen-totales">
            <div class="resumen-row">
                <span class="resumen-label">Subtotal:</span>
                <span class="resumen-value">Q {{ number_format($venta['total_subtotal'] ?? 0, 2) }}</span>
            </div>
            <div class="resumen-row">
                <span class="resumen-label">Descuento total:</span>
                <span class="resumen-value descuento">- Q {{ number_format($venta['total_descuento'] ?? 0, 2) }}</span>
            </div>
            @if(($venta['impuesto'] ?? 0) > 0)
            <div class="resumen-row">
                <span class="resumen-label">Impuesto ({{ $venta['impuesto_porcentaje'] ?? 12 }}%):</span>
                <span class="resumen-value">Q {{ number_format($venta['impuesto'] ?? 0, 2) }}</span>
            </div>
            @endif
            <div class="resumen-row total-final">
                <span class="resumen-label">TOTAL:</span>
                <span class="resumen-value total">Q {{ number_format($venta['total'] ?? 0, 2) }}</span>
            </div>
        </div>

        <!-- Pie de página con WhatsApp en gris -->
        <div class="footer">
            <p class="footer-nombre">LOGICKEM - Variedades Tecnológicas</p>
            <p class="footer-direccion">Dirección: 2da. Calle 6-41 zona 3, Rabinal B.V.</p>
            <p class="footer-whatsapp">WhatsApp: 4710 4888</p>
            <p class="footer-lema">¡Gracias por su preferencia!</p>
        </div>
    </div>
</body>
</html>