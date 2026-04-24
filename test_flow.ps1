$base = 'http://127.0.0.1:8016';
$cookieFile = 'D:\salsabilatrack\cookies.txt';

# 1) GET admin page to get CSRF token and session
$response = Invoke-WebRequest -Uri ($base + '/admin') -UseBasicParsing -TimeoutSec 10;
$token = [regex]::Match($response.Content, 'name="csrf-token" content="([^"]+)"').Groups[1].Value;
Write-Host "Token:" $token.Substring(0,20) "...";

# 2) POST new order
$body = '{"customer_name":"PowerShellTest","order_items":"Mie Ayam"}';
$headers = @{
    'Content-Type' = 'application/json'
    'X-CSRF-TOKEN' = $token
};
$post = Invoke-WebRequest -Uri ($base + '/admin/orders') -Method Post -Body $body -Headers $headers -TimeoutSec 10 -UseBasicParsing;
Write-Host "POST Status:" $post.StatusCode;
$postData = $post.Content | ConvertFrom-Json;
Write-Host "Success:" $postData.success;

# 3) GET orders JSON
$get = Invoke-WebRequest -Uri ($base + '/admin/orders/json') -UseBasicParsing -TimeoutSec 10;
$data = $get.Content | ConvertFrom-Json;
Write-Host "Orders count:" $data.orders.data.Count;
$data.orders.data | ForEach-Object { Write-Host $_.order_number $_.customer_name "est:" $_.estimated_duration "min" };

Remove-Item $cookieFile -Force -ErrorAction SilentlyContinue;
