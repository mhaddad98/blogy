<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 640px;
            padding: 24px;
            background-color: #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        header img {
            height: 32px;
        }

        header p {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            margin: 0;
        }

        main h2 {
            color: #334155;
            font-size: 24px;
            margin-top: 24px;
            margin-bottom: 12px;
        }

        main p {
            color: #475569;
            line-height: 1.6;
            margin: 8px 0;
        }

        .otp-container {
            display: flex;
            gap: 16px;
            margin-top: 16px;
        }

        .otp-box {
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            font-weight: 500;
            color: #2563eb;
            border: 2px solid #2563eb;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <section class="container">
        <header>
            <img src="https://images.rawpixel.com/image_png_800/czNmcy1wcml2YXRlL3Jhd3BpeGVsX2ltYWdlcy93ZWJzaXRlX2NvbnRlbnQvbHIvdjEwNDktMjIucG5n.png"
                alt="logo">
            <p>Blogy</p>
        </header>

        <main>
            <h2>Hi {{ $name }},</h2>
            <p>This is your verification code:</p>

            <div class="otp-container">
                @foreach (str_split($otp) as $char)
                    <div class="otp-box">{{ $char }}</div>
                @endforeach

            </div>

            <p>This code will only be valid for the next 5 minutes.</p>

            <p>Thanks, <br> Blogy Team</p>
        </main>
    </section>
</body>

</html>
