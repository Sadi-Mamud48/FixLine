<!DOCTYPE html> 
<html lang="en"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>FixLine - Admin Login</title>     
    <link rel="stylesheet" href="View/Administrator/style.css">     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
</head> 
<body style="background: radial-gradient(circle at center, #785be8 0%, #3e288c 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh;">      
    <div class="profile-card-overlay" style="max-width: 440px; width: 90%;">         
        <div class="profile-card-header" style="text-align: center;">             
            <div style="font-size: 38px; color: #ffdd59; margin-bottom: 8px;"><i class="fa-solid fa-wrench"></i></div>             
            <h2>FixLine Admin Portal</h2>             
            <p style="color: #ccc; font-size: 13px; margin-top: 5px;">Web-Based Household Repair Platform</p>         
        </div>          

        <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>             
            <div style="background: #e74c3c; color: #fff; padding: 10px; border-radius: 6px; text-align: center; font-size: 13px; margin-bottom: 18px;">                 
                Invalid Email or Password! Try again.             
            </div>         
        <?php endif; ?>          

        <form action="index.php?action=do_login" method="POST">             
            <div class="form-group">                 
                <label>Admin Email Address</label>                 
                <input type="email" name="email" class="form-control" placeholder="admin@fixline.com" value="admin@fixline.com" required>             
            </div>              

            <div class="form-group">                 
                <label>Password</label>                 
                <input type="password" name="password" class="form-control" placeholder="••••••••" value="admin123" required>             
            </div>              

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; font-size: 13px; color: #eee;">                 
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">                     
                    <input type="checkbox" name="remember" checked> Keep me logged in (Cookie)                 
                </label>             
            </div>              

            <button type="submit" class="btn-purple" style="width: 100%; padding: 12px; font-size: 16px;">                 
                Sign In to Dashboard             
            </button>         
        </form>          

        <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #bbb;">             
            Demo Credentials: <strong>admin@fixline.com</strong> / <strong>admin123</strong>         
        </div>     
    </div>  
</body> 
</html>