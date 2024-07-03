<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- favicon -->
    <link rel="icon" href="{{ asset('images/logo-small.png') }}">
    <title>404 - Page Not Found</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        @import url("https://fonts.googleapis.com/css?family=Montserrat:700");

        body {
            font-family: "Montserrat", "sans-serif";
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="flex flex-col items-center justify-center h-screen">
        <h1 class="text-4xl mb-4"> <span class="ascii">(╯°□°）╯︵ ┻━┻</span></h1>
        <h1 class="text-4xl mb-2">404 Page Not Found</h1>
        <span class="mb-4">The page you are looking for could not be found.</span>
        <a href="/" class="text-dark-500 underline  hover:underline">Go back</a>
    </div>
</body>

</html>
