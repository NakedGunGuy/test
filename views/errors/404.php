<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #000000 0%, #0a0a0a 100%);
            color: #FFFFFF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: rgba(0, 174, 239, 0.05);
            border: 1px solid rgba(0, 174, 239, 0.2);
            border-radius: 16px;
            padding: 60px 40px;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
        }
        .error-code {
            font-size: 72px;
            font-weight: 700;
            background: linear-gradient(135deg, #00AEEF 0%, #0098d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 16px;
            font-weight: 600;
        }
        p {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            margin-bottom: 32px;
            font-size: 16px;
        }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #00AEEF 0%, #0098d4 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 4px 16px rgba(0, 174, 239, 0.3);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(0, 174, 239, 0.4);
        }
        .links {
            margin-top: 24px;
        }
        .links a {
            color: #00AEEF;
            text-decoration: none;
            margin: 0 12px;
            transition: color 0.2s ease;
        }
        .links a:hover {
            color: #0098d4;
        }
        @media (max-width: 640px) {
            .error-code {
                font-size: 56px;
            }
            h1 {
                font-size: 24px;
            }
            .error-container {
                padding: 40px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1>Page Not Found</h1>
        <p>
            The page you're looking for doesn't exist or has been moved.
        </p>
        <a href="/" class="btn">Return Home</a>
        <div class="links">
            <a href="/discover">Discover Cards</a>
            <a href="/shop">Shop</a>
        </div>
    </div>
</body>
</html>
