{{--
/**
 * Disparador WhatsApp - Sistema de Disparo de Mensagens
 * 
 * @package DisparadorWhatsApp
 * @author Emerson <https://github.com/emer5om>
 * @version 1.0.0
 * @license MIT
 * @link https://github.com/emer5om/disparador
 */
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Code - {{ $instance->name }}</title>
    @vite(['resources/css/app.css'])
    
    <!-- Auto refresh every 30 seconds -->
    <meta http-equiv="refresh" content="30">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 40px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        
        .status-connected { 
            border: 3px solid #22c55e;
            box-shadow: 0 0 30px rgba(34, 197, 94, 0.3);
        }
        .status-connecting { 
            border: 3px solid #f59e0b;
            box-shadow: 0 0 30px rgba(245, 158, 11, 0.3);
        }
        .status-disconnected { 
            border: 3px solid #ef4444;
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.3);
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-connected .status-badge {
            background: #dcfce7;
            color: #166534;
        }
        .status-connecting .status-badge {
            background: #fef3c7;
            color: #92400e;
        }
        .status-disconnected .status-badge {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .qr-section {
            margin: 30px 0;
        }
        
        .qr-box {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            padding: 20px;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .qr-code {
            width: 250px;
            height: 250px;
            display: block;
        }
        
        .placeholder {
            width: 250px;
            height: 250px;
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6b7280;
        }
        
        .instructions {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            text-align: left;
        }
        
        .instructions h3 {
            color: #1e40af;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        
        .instructions ol {
            color: #1e40af;
            font-size: 14px;
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 6px;
        }
        
        .connected-state {
            padding: 40px 20px;
        }
        
        .connected-icon {
            width: 80px;
            height: 80px;
            background: #dcfce7;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        
        .instance-key {
            background: #f3f4f6;
            padding: 4px 8px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 11px;
            color: #374151;
        }
        
        .refresh-btn {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
        
        @media (max-width: 480px) {
            body { padding: 10px; }
            .container { padding: 30px 25px; }
            .qr-code, .placeholder { width: 200px; height: 200px; }
            .title { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="container 
        @if($instance->status === 'connected') status-connected 
        @elseif($instance->status === 'connecting') status-connecting 
        @else status-disconnected @endif">
        
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <svg width="32" height="32" fill="white" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.064 3.687"/>
                </svg>
            </div>
            <h1 class="title">{{ $instance->name }}</h1>
            
            <!-- Status Badge -->
            <div class="status-badge">
                @if($instance->status === 'connected')
                    <div class="status-dot" style="background-color: #22c55e;"></div>
                    ✅ Conectado
                @elseif($instance->status === 'connecting')
                    <div class="status-dot" style="background-color: #f59e0b;"></div>
                    ⏳ Conectando
                @else
                    <div class="status-dot" style="background-color: #ef4444;"></div>
                    ❌ Desconectado
                @endif
            </div>
        </div>

        @if($instance->status === 'connected')
            <!-- Connected State -->
            <div class="connected-state">
                <div class="connected-icon">
                    <svg width="40" height="40" fill="#22c55e" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 style="font-size: 20px; font-weight: 600; color: #166534; margin-bottom: 10px;">WhatsApp Conectado!</h2>
                <p style="color: #15803d; margin-bottom: 20px;">Sua instância está conectada e pronta para uso</p>
                <div style="font-size: 12px; color: #6b7280;">
                    <p>Conectado em {{ $instance->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        @else
            <!-- QR Code Section -->
            <div class="qr-section">
                @if($instance->qr_code)
                    <div class="qr-box">
                        <img src="data:image/png;base64,{{ $instance->qr_code }}" 
                             alt="QR Code WhatsApp" 
                             class="qr-code">
                    </div>
                    <p style="color: #6b7280; font-size: 14px;">Escaneie este código com seu WhatsApp</p>
                @else
                    <div class="placeholder">
                        <svg width="60" height="60" fill="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11a9 9 0 11-18 0 9 9 0 0118 0zm-9 8a1 1 0 100-2 1 1 0 000 2z"></path>
                        </svg>
                        <p style="font-weight: 500; margin: 10px 0 5px;">QR Code não disponível</p>
                        <p style="font-size: 12px;">Aguarde ou atualize a página</p>
                    </div>
                @endif
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" style="margin-right: 8px;">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Como conectar:
                </h3>
                <ol>
                    <li>Abra o WhatsApp no seu celular</li>
                    <li>Toque em "Menu" (⋮) e depois em "Dispositivos conectados"</li>
                    <li>Toque em "Conectar um dispositivo"</li>
                    <li>Escaneie o código QR acima</li>
                </ol>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-info">
                <span>Atualiza em 30s</span>
                <span>{{ $instance->updated_at->diffForHumans() }}</span>
            </div>
            <p style="text-align: center; font-size: 12px; color: #6b7280; margin-bottom: 15px;">
                <span class="instance-key">{{ $instance->instance_key }}</span>
            </p>
            
            <button onclick="window.location.reload()" class="refresh-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Atualizar Agora
            </button>
        </div>
    </div>
</body>
</html>