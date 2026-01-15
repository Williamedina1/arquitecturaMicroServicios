$BaseUrl = "http://localhost:8012"

function Test-Endpoint {
    param($Title, $Method, $Uri, $Body = $null)
    Write-Host "`n==========================================" -ForegroundColor Cyan
    Write-Host $Title -ForegroundColor Yellow
    Write-Host "Request: $Method $BaseUrl$Uri" -ForegroundColor DarkGray
    
    try {
        $Params = @{
            Uri = "$BaseUrl$Uri"
            Method = $Method
            ContentType = "application/json"
            ErrorAction = "Stop"
        }
        if ($Body) {
            $Params.Body = ($Body | ConvertTo-Json -Depth 5)
            Write-Host "Payload: $($Params.Body)" -ForegroundColor DarkGray
        }

        $Response = Invoke-RestMethod @Params
        Write-Host "Response:" -ForegroundColor Green
        # Manejo especial para ver la propiedad 'data' si existe, o todo el objeto
        if ($Response.data) {
            $Response.data | ConvertTo-Json -Depth 5 | Write-Host
        } else {
            $Response | ConvertTo-Json -Depth 5 | Write-Host
        }
        return $Response
    } catch {
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
        if ($_.Exception.Response) {
            $Stream = $_.Exception.Response.GetResponseStream()
            $Reader = New-Object System.IO.StreamReader($Stream)
            Write-Host $Reader.ReadToEnd() -ForegroundColor Red
        }
    }
}

# 1. Calcular Costo (Simulación de lógica de negocio)
Test-Endpoint -Title "1. PROBAR CALCULO DE COSTO (Endpoint: /shipping/calculate)" -Method POST -Uri "/shipping/calculate" -Body @{
    address = "Av. Siempre Viva 742"
    shipping_method = "express"
}

# 2. Crear un Envio (Simulando que llega de una orden)
# Nota: Como el servicio de Orders no está corriendo, la validación estricta está desactivada en el código para permitir esta prueba.
$NewShipping = Test-Endpoint -Title "2. CREAR NUEVO ENVIO (Endpoint: /shipping)" -Method POST -Uri "/shipping" -Body @{
    order_id = 101
    address = "Calle Falsa 123"
    shipping_method = "standard"
    cost = 15.50
}

if ($NewShipping) {
    # Capturamos el ID creado para usarlo en las siguientes pruebas
    $Id = $NewShipping.data.id

    # 3. Listar Todos
    Test-Endpoint -Title "3. LISTAR TODOS LOS ENVIOS (Endpoint: /shipping)" -Method GET -Uri "/shipping"

    # 4. Obtener Detalle por ID
    Test-Endpoint -Title "4. OBTENER DETALLE DEL ENVIO ID: $Id (Endpoint: /shipping/$Id)" -Method GET -Uri "/shipping/$Id"

    # 5. Obtener Detalle por Order ID
    Test-Endpoint -Title "5. BUSCAR POR ORDER ID: 101 (Endpoint: /shipping/order/101)" -Method GET -Uri "/shipping/order/101"

    # 6. Actualizar Estado
    Test-Endpoint -Title "6. ACTUALIZAR ESTADO Y TRACKING (Endpoint: /shipping/$Id)" -Method PUT -Uri "/shipping/$Id" -Body @{
        status = "shipped"
        tracking_number = "TRACK-99887766"
    }
    
    # 7. Verificación Final
     Test-Endpoint -Title "7. VERIFICAR CAMBIOS (Endpoint: /shipping/$Id)" -Method GET -Uri "/shipping/$Id"
}
